<?php

namespace App\Repository;

use App\Entity\AreaSalud;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AreaSalud|null find($id, $lockMode = null, $lockVersion = null)
 * @method AreaSalud|null findOneBy(array $criteria, array $orderBy = null)
 * @method AreaSalud[]    findAll()
 * @method AreaSalud[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AreaSaludRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AreaSalud::class);
    }

    // /**
    //  * @return AreaSalud[] Returns an array of AreaSalud objects
    //  */

    public function getAreas($provincia,$municipio)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.municipio = :val')
            ->andWhere('a.provincia = :val1')

            ->setParameter('val', $municipio)            ->setParameter('val1', $provincia)

            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?AreaSalud
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
