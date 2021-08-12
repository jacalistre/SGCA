<?php

namespace App\Repository;

use App\Entity\Cama;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cama|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cama|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cama[]    findAll()
 * @method Cama[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CamaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cama::class);
    }

    // /**
    //  * @return Cama[] Returns an array of Cama objects
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
    public function findOneBySomeField($value): ?Cama
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


  public function EncontrarUltimaCama($idcentro,$idsala): ?Cama
  {


      return $this->createQueryBuilder('c')
          ->andWhere('c.centro= :val')
          ->andWhere('c.sala= :val1')
          ->setParameter('val', $idcentro)  ->setParameter('val1', $idsala)->orderBy('c.numero','desc')->setMaxResults(1)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }


    public function ActualizarEstadoCama($idcama,$estado)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Cama', 'p');
        return         $this->getEntityManager()->createNativeQuery("Update cama set estado_id=:val where id=:val1" ,$rsm)
           ->setParameter("val",$estado)
            ->setParameter('val1', $idcama)
            ->execute()
        ;
    }

}
