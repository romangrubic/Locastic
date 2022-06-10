<?php

/**
 * This file contains Results repository class and methods
 */

namespace App\Repository;

use App\Entity\Results;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Results>
 *
 * @method Results|null find($id, $lockMode = null, $lockVersion = null)
 * @method Results|null findOneBy(array $criteria, array $orderBy = null)
 * @method Results[]    findAll()
 * @method Results[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultsRepository extends ServiceEntityRepository
{    
    /**
     * __construct
     *
     * @param  ManagerRegistry $registry
     * @return void
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Results::class);
    }
    
    /**
     * add
     *
     * @param  Results $entity
     * @param  bool $flush
     * @return void
     */
    public function add(Results $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * remove
     *
     * @param  Results $entity
     * @param  bool $flush
     * @return void
     */
    public function remove(Results $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Find results by distance. Return only race time
     *
     * @param  int $id
     * @param  string $distance
     * @return array
     */
    public function findTimeByDistance(int $id, string $distance): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.raceTime')
           ->andWhere('r.race = :id')
           ->andWhere('r.distance = :distance')
           ->setParameter('id', $id)
           ->setParameter('distance', $distance)
           ->getQuery()
           ->getResult()
       ;
    }
}
