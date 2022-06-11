<?php

/**
 * This file contains CSV service class and methods to upload file, read and write file to DB, and delete file after that.
 */
namespace App\Services;

use App\Entity\Results;
use Doctrine\ORM\EntityManagerInterface;
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
     * Read CSV file and prepare data
     *
     * @param  string $filename
     * @return array
     */
    public function readCSV(string $filename): array
    {
        $csvFile = file_get_contents($this->container->getParameter('uploads_dir') . '/' . $filename);
        $lines = explode(PHP_EOL, $csvFile);
        $data = [];

        foreach ($lines as $line) {
            $data[] = str_getcsv($line);
        }

        return $data;
    }
    
    /**
     * Insert data into DB
     *
     * @param  mixed $race
     * @param  array $data
     * @return void
     */
    public function insertIntoDB($race, $data): void
    {
        foreach ($data as $row) {
            /**
             * Skips first row that has row names (not row data) as in example CSV
             */
            if ($row[0] == "fullName") {
                continue;
            }

            /**
             * Skip if row doesn't have all three data points.
             */
            if (count($row) != 3) {
                continue;
            }

            /**
             * Adding zero if needed for hours
             * Format is xx:xx:xx
             */
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