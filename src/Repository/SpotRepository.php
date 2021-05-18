<?php

namespace App\Repository;

use App\Entity\Deporte;
use App\Entity\Spot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Spot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Spot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Spot[]    findAll()
 * @method Spot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spot::class);
    }

    /**
     * @param int $cant
     * @param Deporte|null $deporte
     * @param string $orderBy
     * @return Spot[] Returns an array of Spot objects
     */
    public function findSpotsBy(int $cant, Deporte $deporte = null, string $orderBy = 'notaMedia'): array
    {
        if ($deporte === null) {
            $depRepo = $this->getEntityManager()->getRepository(Deporte::class);
            $deporte = $depRepo->findOneBy(['nombre' => 'skate']);
        }

        return $this->createQueryBuilder('s')
            ->andWhere('s.deporte = :deporte')
            ->andWhere('s.aprobado = true')
            ->orderBy('s.' . $orderBy, 'DESC')
            ->setMaxResults($cant)
            ->setParameter('deporte', $deporte)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Spot
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
