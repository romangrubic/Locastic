<?php

namespace App\Services;

use App\Entity\Race;
use App\Entity\Results;
use App\Repository\RaceRepository;
use App\Services\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;



class Calculate
{
    private $em;
    private $doctrine;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $this->em = $em;
        $this->doctrine = $doctrine;
    }


    public function placement($lastId, $string)
    {
        $results = $this->doctrine->getRepository(Results::class)->findBy(['race' => $lastId, 'distance' => $string], ['raceTime' => 'ASC']);

                $placement = 1;

                foreach ($results as $row) {
                    $result = $row->setPlacement($placement);

                    $this->em->persist($result);

                    $placement++;
                }
                $this->em->flush();
    }

}