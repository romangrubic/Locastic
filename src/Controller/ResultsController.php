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

            // Adding 0 as first char if needed
            if (strpos($result->getRaceTime(), ':') == 1 ) {
                $result->setRaceTime('0' . $result->getRaceTime()); 
            }

            $resultsRepository->add($result, true);

            // Recalculating placements
            $calculate->placement($result->getRace()->getId(), $result->getDistance());
            

            return $this->redirectToRoute('app_race_show', ['id' => $result->getRace()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('results/edit.html.twig', [
            'result' => $result,
            'form' => $form,
        ]);
    }
}
