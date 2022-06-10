<?php

/**
 * This file contains Calculate class and helper methods for calculatin placement order and average time by distance
 */

namespace App\Services;

use App\Repository\ResultsRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Calculate class is a service class 
 */
class Calculate
{    
    /**
     * Setting properties
     */
    private EntityManagerInterface $em; 
    private ResultsRepository $resultsRepository;
    
    /**
     * __construct
     *
     * @param  EntityManagerInterface $em
     * @param  ResultsRepository $resultsRepository
     * @return void
     */
    public function __construct(EntityManagerInterface $em, ResultsRepository $resultsRepository)
    {
        $this->em = $em;
        $this->resultsRepository = $resultsRepository;
    }
   
    /**
     * Calculates placement after insert and each edit Result, based on distance
     *
     * @param  int $lastId
     * @param  string $string
     * @return void
     */
    public function placement(int $lastId, string $string): void
    {
        $results = $this->resultsRepository->findBy(['race' => $lastId, 'distance' => $string], ['raceTime' => 'ASC']);
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
     * Not the best I'll admit that, but does its job.
     *
     * @param mixed $results
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