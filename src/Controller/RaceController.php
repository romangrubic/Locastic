<?php

/**
 * This file contains controller for Race (Race entity).
 */

namespace App\Controller;

use App\{Entity\Race,
    Form\RaceType};
use App\Repository\{RaceRepository,
    ResultsRepository};
use App\Services\{Calculate,
    ImportCSV};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,
    Response};
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/")
 */
class RaceController extends AbstractController
{   
    /**
     * Setting properties
     */
    private RaceRepository $raceRepository;
    private ImportCSV $importCSV;
    private Calculate $calculate;
    private EntityManagerInterface $em;
    private ResultsRepository $resultsRepository;
    
    
    /**
     * __construct
     *
     * @param  RaceRepository $raceRepository
     * @param  ImportCSV $importCSV
     * @param  Calculate $calculate
     * @param  EntityManagerInterface $em
     * @param  ResultsRepository $resultsRepository
     * @return void
     */
    public function __construct(RaceRepository $raceRepository,
                                ImportCSV $importCSV,
                                Calculate $calculate,
                                EntityManagerInterface $em,
                                ResultsRepository $resultsRepository)
    {
        $this->raceRepository = $raceRepository;
        $this->importCSV = $importCSV;
        $this->calculate = $calculate;
        $this->em = $em;
        $this->resultsRepository = $resultsRepository;

    }

    /**
     * @Route("/", name="app_race_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $race = new Race();

        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('race')['file'];

            if ($file) {
                /**
                 * Storing uploaded CSV file
                 */
                $filename = $this->importCSV->upload($file);

                /**
                 * Storing Race object
                 */
                $this->em->persist($race);
                $this->em->flush();
        
                /**
                 * Reads CSV file and inserts Results data into DB
                 */
                $this->importCSV->writeIntoDb($race, $filename);

                /**
                 * Calculating distance placements
                 */
                $distances = ['medium', 'long'];

                foreach ($distances as $distance) {
                    $this->calculate->placement($race->getId(), $distance);
                }

                /**
                 * Deleting uploaded file
                 */
                $this->importCSV->delete($filename);
            }

            return $this->redirectToRoute('app_race_show', ['id' => $race->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('race/new.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_race_show", methods={"GET"})
     */
    public function show(int $id): Response
    {
        /**
         * Calculate medium average
         */
        $resultsMedium = $this->resultsRepository->findTimeByDistance($id, 'medium');
        $avgMedium = $this->calculate->average($resultsMedium);

        /**
         * Calculate long average
         */
        $resultsLong = $this->resultsRepository->findTimeByDistance($id, 'long');
        $avgLong = $this->calculate->average($resultsLong);

    
        return $this->render('results/index.html.twig', [
            'avgMedium' => $avgMedium,
            'avgLong' => $avgLong,
            'race' => $this->raceRepository->findOneBy(['id' => $id]),
            'distanceMedium' => $this->resultsRepository->findBy(['race' => $id, 'distance' => 'medium'], ['placement' => 'ASC']),
            'distanceLong' => $this->resultsRepository->findBy(['race' => $id, 'distance' => 'long'], ['placement' => 'ASC']),
        ]);
    }

    /**
     * @Route("/all", name="app_race_index", methods={"GET"})
     */
    public function showAll(): Response
    {
        return $this->render('race/index.html.twig', [
            'races' => $this->raceRepository->findAll(),
        ]);
    }
}
