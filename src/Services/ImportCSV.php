<?php

namespace App\Services;

use App\Entity\Results;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

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

    public function writeIntoDb($race, $filename)
    {
        $reader = Reader::createFromPath($this->container->getParameter('uploads_dir') . '/' . $filename, 'r');

        $results = $reader->getRecords();

        $rowNumber = 1;

                foreach ($results as $row) {
                    if ($rowNumber == 1){
                        $rowNumber++;
                    }
                    // dd($row);
                    // die;
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

    public function delete($filename)
    {
        unlink($this->container->getParameter('uploads_dir') . '/' . $filename);
    }
}