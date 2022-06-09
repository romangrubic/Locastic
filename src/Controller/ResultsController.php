<?php

namespace App\Controller;

use App\Entity\Results;
use App\Form\ResultsType;
use App\Repository\ResultsRepository;
use App\Services\Calculate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/results")
 */
class ResultsController extends AbstractController
{
    /**
     * @Route("/", name="app_results_index", methods={"GET"})
     */
    public function index(ResultsRepository $resultsRepository): Response
    {
        return $this->render('results/index.html.twig', [
            'results' => $resultsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_results_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ResultsRepository $resultsRepository): Response
    {
        $result = new Results();
        $form = $this->createForm(ResultsType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultsRepository->add($result, true);

            return $this->redirectToRoute('app_results_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('results/new.html.twig', [
            'result' => $result,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_results_show", methods={"GET"})
     */
    public function show(Results $result): Response
    {
        return $this->render('results/show.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_results_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Results $result, ResultsRepository $resultsRepository, Calculate $calculate): Response
    {
        $form = $this->createForm(ResultsType::class, $result);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultsRepository->add($result, true);
            // dd($result->getRace()->getId());

            // Recalculating placements
            $calculate->placement($result->getRace()->getId(), $result->getDistance());
            

            return $this->redirectToRoute('app_race_show', ['id' => $result->getRace()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('results/edit.html.twig', [
            'result' => $result,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_results_delete", methods={"POST"})
     */
    public function delete(Request $request, Results $result, ResultsRepository $resultsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$result->getId(), $request->request->get('_token'))) {
            $resultsRepository->remove($result, true);
        }

        return $this->redirectToRoute('app_results_index', [], Response::HTTP_SEE_OTHER);
    }
}
