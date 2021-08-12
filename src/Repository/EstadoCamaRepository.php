<?php

namespace App\Repository;

use App\Entity\EstadoCama;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstadoCama|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoCama|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoCama[]    findAll()
 * @method EstadoCama[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoCamaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoCama::class);
    }

    // /**
    //  * @return EstadoCama[] Returns an array of EstadoCama objects
    //  */

    public function Obtener($estado)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.estado = :val')
            ->setParameter('val', $estado)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function ObtenerEstados()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.estado not like :val')
            ->setParameter('val', "Ocupada")
            ->getQuery()->getResult();
    }
    /*
    public function findOneBySomeField($value): ?EstadoCama
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
