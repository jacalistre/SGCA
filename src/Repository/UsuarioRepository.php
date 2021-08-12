<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    // /**
    //  * @return Usuario[] Returns an array of Usuario objects
    //  */

    public function ObtenerUsuarios($role,$provincia,$municipio=null)
    {
        $query= $this->createQueryBuilder('u')
            ->andWhere('u.role = :val')
            ->andWhere('u.provincia = :val1')
            ->setParameter('val', $role)
            ->setParameter('val1', $provincia);
        if($municipio!=null){
            $query->andWhere("u.municipio =:val2")
            ->setParameter("val2",$municipio);

        }
        return $query->orderBy('u.id', 'ASC')

            ->getQuery()
            ->getResult()
            ;

    }
    public function ObtenerUsers($provincia,$municipio=null)
    {
        $query= $this->createQueryBuilder('u')
            ->andWhere('u.provincia = :val1')
            ->setParameter('val1', $provincia);
        if($municipio!=null){
            $query->andWhere("u.municipio =:val2")
                ->setParameter("val2",$municipio);

        }
        return $query->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;

    }


    /*
    public function findOneBySomeField($value): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
