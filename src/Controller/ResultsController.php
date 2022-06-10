<?php

/**
 * This file contains controller for Race (Race entity).
 */

namespace App\Controller;

use App\{Entity\Results,
    Form\ResultsType,
    Repository\ResultsRepository,
    Services\Calculate};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,
    Response};
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/results")
 */
class ResultsController extends AbstractController
{
    /**
     * Setting properties
     */
    private ResultsRepository $resultsRepository;
    private Calculate $calculate;
    
    /**
     * __construct
     *
     * @param  ResultsRepository $resultsRepository
     * @param  Calculate $calculate
     * @return void
     */
    public function __construct(ResultsRepository $resultsRepository,
                                Calculate $calculate)
    {
        $this->resultsRepository = $resultsRepository;
        $this->calculate = $calculate;
    }
    
    /**
     * @Route("/{id}/edit", name="app_results_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Results $result ): Response
    {
        $form = $this->createForm(ResultsType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * Adding 0 as first char if needed
             */
            if (strpos($result->getRaceTime(), ':') == 1 ) {
                $result->setRaceTime('0' . $result->getRaceTime()); 
            }
            $this->resultsRepository->add($result, true);

            /**
             * Recalculate placements
             */
            $this->calculate->placement($result->getRace()->getId(), $result->getDistance());

            return $this->redirectToRoute('app_race_show', ['id' => $result->getRace()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('results/edit.html.twig', [
            'result' => $result,
            'form' => $form,
        ]);
    }
}
