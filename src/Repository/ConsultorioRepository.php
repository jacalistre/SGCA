<?php

namespace App\Repository;

use App\Entity\Consultorio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Consultorio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consultorio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consultorio[]    findAll()
 * @method Consultorio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultorioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultorio::class);
    }

    // /**
    //  * @return Consultorio[] Returns an array of Consultorio objects
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
    public function findOneBySomeField($value): ?Consultorio
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
