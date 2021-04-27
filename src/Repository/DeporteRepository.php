<?php

namespace App\Repository;

use App\Entity\Deporte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deporte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deporte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deporte[]    findAll()
 * @method Deporte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeporteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deporte::class);
    }

    // /**
    //  * @return Deporte[] Returns an array of Deporte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Deporte
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
