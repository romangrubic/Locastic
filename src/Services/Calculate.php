<?php

namespace App\Services;

use App\Entity\Results;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Calculate class is a service class 
 */
class Calculate
{    
    /**
     * em
     *
     * @var EntityManagerInterface
     */
    private $em; 
       
    /**
     * doctrine
     *
     * @var ManagerRegistry
     */
    private $doctrine;
    
    /**
     * __construct
     *
     * @param  EntityManagerInterface $em
     * @param  ManagerRegistry $doctrine
     * @return void
     */
    public function __construct(EntityManagerInterface $em, ManagerRegistry $doctrine)
    {
        $this->em = $em;
        $this->doctrine = $doctrine;
    }
   
    /**
     * Calculates placement after insert and each update based on distance
     *
     * @param  int $lastId
     * @param  string $string
     * @return void
     */
    public function placement(int $lastId, string $string): void
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

        
    /**
     * Calculates average time for distance
     *
     * @param  mixed $results
     * @return string
     */
    public function average($results): string
    {
        $count = 0;
        $h = 0;
        $m = 0;
        $s = 0;

        foreach ($results as $row) {
            $hours = (int) substr($row['raceTime'], 0 ,2);
            $h += $hours;

            $minutes = (int) substr($row['raceTime'], 3 ,5);
            $m += $minutes;

            $seconds = (int) substr($row['raceTime'], 6 ,8);
            $s += $seconds;

            $count++;
        }

        $totalSeconds = ($h * 3600) + ($m * 60) + $s;

        $avg = gmdate('H:i:s', $totalSeconds/$count);

        return $avg;
    }
}