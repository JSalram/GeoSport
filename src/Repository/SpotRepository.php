<?php

namespace App\Repository;

use App\Entity\Deporte;
use App\Entity\Spot;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function paginacion($query, int $pagina, int $spotsPagina): Paginator
    {
        $paginador = new Paginator($query);
        $paginador->getQuery()
            ->setFirstResult($spotsPagina * ($pagina - 1))
            ->setMaxResults($spotsPagina);
        return $paginador;
    }

    /**
     * @param int $pagina
     * @param int $spotsPagina
     * @param Deporte|null $deporte
     * @param string $orderBy
     * @param null $provincia
     * @return Paginator Returns an array of Spot objects
     */
    public function findSpotsBy(int $pagina = 1, int $spotsPagina = 5,
                    Deporte $deporte = null, string $orderBy = 'notaMedia', $provincia = null): Paginator
    {
        if ($deporte === null) {
            $depRepo = $this->getEntityManager()->getRepository(Deporte::class);
            $deporte = $depRepo->findOneBy(['nombre' => 'skate']);
        }

        $query = $this->createQueryBuilder('s')
            ->andWhere('s.deporte = :deporte')
            ->andWhere('s.aprobado = true');
            if ($provincia) {
                $query->andWhere('s.provincia = :provincia')
                ->setParameter('provincia', $provincia);
            }
            $query->orderBy('s.' . $orderBy, 'DESC')
            ->setParameter('deporte', $deporte)
            ->getQuery();

        return $this->paginacion($query, $pagina, $spotsPagina);
    }

    public function findByUser(User $user, int $pagina, int $spotsPagina): Paginator
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->orderBy('s.fecha', 'DESC')
            ->setParameter('user', $user)
            ->getQuery();

        return $this->paginacion($query, $pagina, $spotsPagina);
    }
}
