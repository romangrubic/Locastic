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
    Form};
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
    private Calculate $calculate;
    private ResultsRepository $resultsRepository;
    private Form $formService;
    
    /**
     * __construct
     *
     * @param  RaceRepository $raceRepository
     * @param  Calculate $calculate
     * @param  EntityManagerInterface $em
     * @param  ResultsRepository $resultsRepository
     * @return void
     */
    public function __construct(RaceRepository $raceRepository,
                                Calculate $calculate,
                                ResultsRepository $resultsRepository,
                                Form $formService)
    {
        $this->raceRepository = $raceRepository;
        $this->calculate = $calculate;
        $this->resultsRepository = $resultsRepository;
        $this->formService = $formService;
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
                 * Service class for adding new Race and import CSV file
                 */
                $this->formService->insertRaceAndCSV($file, $race);
                
                return $this->redirectToRoute('app_race_show', ['id' => $race->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('race/new.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/race/{id}", name="app_race_show", methods={"GET"})
     */
    public function show(int $id): Response
    {
        /**
         * OPTIMIZATION
         * Number of total queries could be reduced from 5 to 3, but I have issues accessing race time when doing so.
         */

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
