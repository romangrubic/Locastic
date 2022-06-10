<?php

/**
 * This file contains controller for Race (Race entity).
 */

namespace App\Controller;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use App\Repository\ResultsRepository;
use App\Services\{Calculate,
    ImportCSV};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/race")
 */
class RaceController extends AbstractController
{
    /**
     * @Route("/", name="app_race_index", methods={"GET"})
     */
    public function index(RaceRepository $raceRepository): Response
    {
        return $this->render('race/index.html.twig', [
            'races' => $raceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_race_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ImportCSV $importCSV, Calculate $calculate, EntityManagerInterface $em): Response
    {
        $race = new Race();

        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('race')['file'];

            if ($file) {
                // Storing uploaded CSV file
                $filename = $importCSV->upload($file);

                // Storing Race object
                $em->persist($race);
                $em->flush();
        
                // Reads CSV file and inserts Results data into DB
                $importCSV->writeIntoDb($race, $filename);

                // Calculating distance placements
                $distances = ['medium', 'long'];

                foreach ($distances as $distance) {
                    $calculate->placement($race->getId(), $distance);
                }

                // Deleting uploaded file
                $importCSV->delete($filename);
            }

            return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('race/new.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_race_show", methods={"GET"})
     */
    public function show(ResultsRepository $resultsRepository, RaceRepository $race, int $id, Calculate $calculate): Response
    {
        $resultsMedium = $resultsRepository->findTimeByDistance($id, 'medium');

        $avgMedium = $calculate->average($resultsMedium);

        $resultsLong = $resultsRepository->findTimeByDistance($id, 'long');

        $avgLong = $calculate->average($resultsLong);

    
        return $this->render('results/index.html.twig', [
            'avgMedium' => $avgMedium,
            'avgLong' => $avgLong,
            'race' => $race->findOneBy(['id' => $id]),
            'distanceMedium' => $resultsRepository->findBy(['race' => $id, 'distance' => 'medium'], ['placement' => 'ASC']),
            'distanceLong' => $resultsRepository->findBy(['race' => $id, 'distance' => 'long'], ['placement' => 'ASC']),
        ]);
    }
}
