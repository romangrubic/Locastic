<?php

/**
 * This file contains controller for Race (Race entity).
 */

namespace App\Controller;

use App\{Entity\Results,
    Form\ResultsType,
    Services\Form};
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
    private Form $formService;
    
    /**
     * __construct
     *
     * @param  Form $formService
     * @return void
     */
    public function __construct(Form $formService)
    {
        $this->formService = $formService;
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
             * Service for editing result and recalculating placements
             */
            $this->formService->editResult($result);
            return $this->redirectToRoute('app_race_show', ['id' => $result->getRace()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('results/edit.html.twig', [
            'result' => $result,
            'form' => $form,
        ]);
    }
}
