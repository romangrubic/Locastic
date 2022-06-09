<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Race;
use App\Entity\Results;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $result = $doctrine->getRepository(Results::class)->findBy(['race' => 1]);

        // $race = $result->getCategory()->getRaceName();

        // $result = $doctrine->getRepository(Results::class)->find(1);

        // $race = $result->getRace();

        dd($result);
        exit;

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/import", name="import_CSV")
     */
    public function import(Request $request)
    {
        if ($request->isMethod('POST')) {

            dd($request->files->get('csvFile'));

            $file = $form->get('submitFile');

dd($file); die;
            // $dir = $this->get('kernel')->getRootDir() . '/../web/uploads/csv/';
            // $name = uniqid() . '.csv';
    
            // foreach ($request->files as $uploadedFile) {
            //     $uploadedFile->move($dir, $name);
            // }
    
            // $file = $this->get('kernel')->getRootDir() . "/../web/uploads/csv/" . $name;
        }

        return $this->render('import.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
