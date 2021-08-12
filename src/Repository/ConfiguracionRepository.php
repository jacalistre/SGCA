<?php

namespace App\Repository;

use App\Entity\Configuracion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Configuracion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Configuracion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Configuracion[]    findAll()
 * @method Configuracion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfiguracionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuracion::class);
    }

    // /**
    //  * @return Configuracion[] Returns an array of Configuracion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Configuracion
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
