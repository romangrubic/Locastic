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
     * @param array $results
     * @param string $results
     * @return string
     */
    public function average(array $results, string $distance): string
    {
        /**
         * Filling array with race time from only required distance
         */
        $array = [];

        foreach ($results as $result) {
            if($result['distance'] == $distance) {
                $array[] = $result['raceTime'];
            }
        }

        /**
         * Source: https://www.calculatorsoup.com/calculators/time/decimal-to-time-calculator.php
         * 
         * Separates hours, minutes and second from each race time into their respective variable,
         * then multiplying each variable to get that value in seconds.
         * Use gmdate php function to convert given seconds (divided by how many results there are) into hh:mm:ss format
         */
        $count = 0;
        $h = 0;
        $m = 0;
        $s = 0;

        foreach ($array as $row) {
            // dd($row);
            $hours = (int) substr($row, 0 ,2);
            $h += $hours;

            $minutes = (int) substr($row, 3 ,5);
            $m += $minutes;

            $seconds = (int) substr($row, 6 ,8);
            $s += $seconds;

            $count++;
        }

        $totalSeconds = ($h * 3600) + ($m * 60) + $s;

        return gmdate('H:i:s', $totalSeconds/$count);
    }
}