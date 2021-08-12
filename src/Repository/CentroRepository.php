<?php

namespace App\Repository;

use App\Entity\Centro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Centro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Centro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Centro[]    findAll()
 * @method Centro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Centro::class);
    }

    // /**
    //  * @return Centro[] Returns an array of Centro objects
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


    public function findCentrosRol($provincia, $municipio = null)
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.provincia = :val')
            ->setParameter('val', $provincia);
        if ($municipio != null) {
            $query->andWhere('c.municipio = :val1')->andWhere('c.tipo not like :val2')->setParameter('val1', $municipio)->setParameter('val2','%Hospital%');
        };
        return $query->getQuery()->getResult();
    }

}
