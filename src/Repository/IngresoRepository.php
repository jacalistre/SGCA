<?php

namespace App\Repository;

use App\Entity\Ingreso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ingreso|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingreso|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingreso[]    findAll()
 * @method Ingreso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngresoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingreso::class);
    }

    // /**
    //  * @return Ingreso[] Returns an array of Ingreso objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */




}
