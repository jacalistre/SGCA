<?php

namespace App\Repository;

use App\Entity\Prueba;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prueba|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prueba|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prueba[]    findAll()
 * @method Prueba[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PruebaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prueba::class);
    }

    // /**
    //  * @return Prueba[] Returns an array of Prueba objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function ObtenerEvolutivo($idingreso): ?Prueba
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.ingreso = :val')
            ->andWhere('p.resultado is null')
            ->setParameter('val', $idingreso)
            ->orderBy("p.fecha","DESC")
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

}
