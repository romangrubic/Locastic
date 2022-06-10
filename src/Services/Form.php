<?php

/**
 * This file contains CSV
 */
namespace App\Services;

use App\Entity\Results;
use App\Repository\ResultsRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Form class is a service class 
 */
class Form
{
    /**
     * Setting properties
     */
    private ResultsRepository $resultsRepository;
    private Calculate $calculate;
    private CSV $CSV;
    private EntityManagerInterface $em;
    
    /**
     * __construct
     *
     * @param  ResultsRepository $resultsRepository
     * @param  Calculate $calculate
     * @param  CSV $CSV
     * @param  CalcuEntityManagerInterfacelate $em
     * @return void
     */
    public function __construct(ResultsRepository $resultsRepository, 
                                Calculate $calculate,
                                CSV $CSV,
                                EntityManagerInterface $em)
    {
        $this->resultsRepository = $resultsRepository;
        $this->calculate = $calculate;
        $this->CSV = $CSV;
        $this->em = $em;
    }
    
    /**
     * insertRaceAndCSV
     *
     * @param  mixed $file
     * @param  mixed $race
     * @return void
     */
    public function insertRaceAndCSV($file, $race): void
    {
        /**
         * Storing uploaded CSV file
         */
        $filename = $this->CSV->upload($file);

        /**
         * Storing Race object
         */
        $this->em->persist($race);
        $this->em->flush();

        /**
         * Reads CSV file and inserts Results data into DB
         */
        $this->CSV->writeIntoDb($race, $filename);

        /**
         * Calculating distance placements
         */
        $distances = ['medium', 'long'];

        foreach ($distances as $distance) {
            $this->calculate->placement($race->getId(), $distance);
        }

        /**
         * Deleting uploaded file
         */
        $this->CSV->delete($filename);
    }
        
    /**
     * Edit result and call service for recalculating placement order
     *
     * @param  Results $result
     * @return void
     */
    public function editResult(Results $result): void
    {
        /**
         * Adding 0 as first char if needed
         */
        if (strpos($result->getRaceTime(), ':') == 1 ) {
            $result->setRaceTime('0' . $result->getRaceTime()); 
        }
        $this->resultsRepository->add($result, true);

        /**
         * Recalculate placements
         */
        $this->calculate->placement($result->getRace()->getId(), $result->getDistance());
    }
}