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
         * Fetch all results here so that I can calculate averages
         */
        $allResults = $this->resultsRepository->findResultsByRaceId($id);

        return $this->render('results/index.html.twig', [
            'avgMedium' => $this->calculate->average($allResults, 'medium'),
            'avgLong' => $this->calculate->average($allResults, 'long'),
            'race' => $this->raceRepository->findOneBy(['id' => $id]),
            'allResults' => $allResults,
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
