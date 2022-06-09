<?php

namespace App\Controller;

use App\Entity\Race;
use App\Entity\Results;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use App\Repository\ResultsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use LDAP\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Csv\Reader;

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
    public function new(ManagerRegistry $doctrine, Request $request, RaceRepository $raceRepository): Response
    {
        $race = new Race();

        $form = $this->createForm(RaceType::class, $race);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $file = $request->files->get('race')['file'];

            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename
                );
                
                $em->persist($race);
                $em->flush();

                // Last id
                $lastId = $race->getId();

                // dd($lastId);
            
                $reader = Reader::createFromPath($this->getParameter('uploads_dir') . '/' . $filename, 'r');

                $results = $reader->getRecords();

                $rowNumber = 1;

                foreach ($results as $row) {
                    if ($rowNumber == 1){
                        $rowNumber++;
                    }
                    // dd($row);
                    // die;
                    $result = (new Results())
                        ->setRace($race)
                        ->setFullName($row[0])
                        ->setDistance($row[1])
                        ->setRaceTime($row[2]);

                    $em->persist($result);
                }

                $em->flush();

                // Medium distance placements

                $results = $doctrine->getRepository(Results::class)->findBy(['race' => $lastId, 'distance' => 'medium'], ['raceTime' => 'ASC']);

                $placement = 1;

                foreach ($results as $row) {
                    $result = $row->setPlacement($placement);

                    $em->persist($result);

                    $placement++;
                }
                $em->flush();

                //  Long distance placements 

                $results = $doctrine->getRepository(Results::class)->findBy(['race' => $lastId, 'distance' => 'long'], ['raceTime' => 'ASC']);

                $placement = 1;

                foreach ($results as $row) {
                    $result = $row->setPlacement($placement);

                    $em->persist($result);

                    $placement++;
                }
                $em->flush();

                // Deleting uploaded file
                unlink($this->getParameter('uploads_dir') . '/' . $filename);

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
    public function show(ResultsRepository $resultsRepository, int $id): Response
    {
        dd($resultsRepository->mediumAverage($id));

        return $this->render('results/index.html.twig', [
            'distanceMediumAverage' => $resultsRepository->mediumAverage($id),
            'distanceMedium' => $resultsRepository->findBy(['race' => $id, 'distance' => 'medium'], ['placement' => 'ASC']),
            'distanceLong' => $resultsRepository->findBy(['race' => $id, 'distance' => 'long'], ['placement' => 'ASC']),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_race_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Race $race, RaceRepository $raceRepository): Response
    {
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raceRepository->add($race, true);

            return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('race/edit.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_race_delete", methods={"POST"})
     */
    public function delete(Request $request, Race $race, RaceRepository $raceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$race->getId(), $request->request->get('_token'))) {
            $raceRepository->remove($race, true);
        }

        return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
    }
}
