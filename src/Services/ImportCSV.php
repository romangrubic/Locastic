<?php

namespace App\Services;

use App\Entity\Race;
use App\Entity\Results;
use App\Services\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\DependencyInjection\ContainerInterface as DependencyInjectionContainerInterface;

class ImportCSV
{
    private $container;
    private $em;

    public function __construct(DependencyInjectionContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }


    public function upload($file)
    {

        $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

                $file->move(
                    $this->container->getParameter('uploads_dir'),
                    $filename
                );

        return $filename;
    }

    public function writeIntoDb($results, $race)
    {
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

                    $this->em->persist($result);
                }

                $this->em->flush();
    }

    public function delete($filename)
    {
        unlink($this->container->getParameter('uploads_dir') . '/' . $filename);
    }
}