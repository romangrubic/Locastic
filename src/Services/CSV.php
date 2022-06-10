<?php

/**
 * This file contains CSV service class and methods to upload file, read and write file to DB, and delete file after that.
 */
namespace App\Services;

use App\Entity\Results;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CSV class is a service class 
 */
class CSV
{
    /**
     * Setting properties
     */
    private $container;
    private $em;
    
    /**
     * __construct
     *
     * @param  ContainerInterface $container
     * @param  EntityManagerInterface $em
     * @return void
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * Upload File on server
     *
     * @param  mixed $file
     * @return string
     */
    public function upload($file):string
    {
        $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

        $file->move(
            $this->container->getParameter('uploads_dir'),
            $filename
        );

        return $filename;
    }
    
    /**
     * Read and write CSV rows into result table
     *
     * @param  mixed $race
     * @param  string $filename
     * @return void
     */
    public function writeIntoDb($race, string $filename):void
    {
        $reader = Reader::createFromPath($this->container->getParameter('uploads_dir') . '/' . $filename, 'r');

        $results = $reader->getRecords();

        $rowNumber = 1;

                foreach ($results as $row) {
                    /**
                     * Skips first row that has row names (not row data)
                     */
                    if ($rowNumber == 1){
                        $rowNumber++;
                        continue;
                    }

                    if (strpos($row[2], ':') == 1 ) {
                        $row[2] = '0' . $row[2]; 
                    }

                    $result = (new Results())
                        ->setRace($race)
                        ->setFullName($row[0])
                        ->setDistance($row[1])
                        ->setRaceTime($row[2]);

                    $this->em->persist($result);
                }

                $this->em->flush();
    }
    
    /**
     * Delete CSV file after inserting into DB
     *
     * @param  string $filename
     * @return void
     */
    public function delete(string $filename):void
    {
        unlink($this->container->getParameter('uploads_dir') . '/' . $filename);
    }
}