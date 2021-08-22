<?php

namespace App\Repository;

use App\Entity\Notificacion;
use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notificacion[]    findAll()
 * @method Notificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notificacion::class);
    }

    // /**
    //  * @return Notificacion[] Returns an array of Notificacion objects
    //  */

    public function ObtenerNotificacionesdelPaciente($id)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.paciente = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Notificacion
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function ObtenerNotificaciones($usuario)
    {

        $query = $this->createQueryBuilder('u')
            ->andWhere('u.destino = :val')
            ->setParameter("val", $usuario->getId())
            ->getQuery()->getResult();

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Notificacion', 'n');
        $query1 = $this->getEntityManager()->createNativeQuery("SELECT n.* FROM notificacion n join usuario u on n.origen_id=u.id where n.destino = :val1 and u.municipio_id = :val2 and u.provincia_id= :val3 ", $rsm)
            ->setParameter('val1', $usuario->getRoles())
            ->setParameter('val3', $usuario->getProvincia()->getId())
            ->setParameter('val2', $usuario->getMunicipio()->getId())
            ->getResult();

        return array_merge($query, $query1);
    }

    public function ObtenerNotificacionesSinLeer($usuario)
    {

        $query = $this->createQueryBuilder('u')
            ->andWhere('u.destino = :val')
            ->andWhere('u.fecha_leido is null')
            ->setParameter("val", $usuario->getId())
            ->getQuery()->getResult();
        $query1 = [];
       // if ($usuario->getRoles() == "ROLE_COORDINADOR_MUNICIPAL" or $usuario->getRoles() == "ROLE_COORDINADOR_PROVINCIAL" ) {
            $rsm = new ResultSetMappingBuilder($this->getEntityManager());
            $rsm->addRootEntityFromClassMetadata('App\Entity\Notificacion', 'n');
            $query1 = $this->getEntityManager()->createNativeQuery("Select n.* from notificacion n  join usuario u on n.origen_id=u.id where n.destino=:rol and u.provincia_id=:provincia and u.municipio_id=:municipio and n.fecha_leido is null ", $rsm)
                ->setParameter('rol', $usuario->getRoles())
                ->setParameter('municipio', $usuario->getMunicipio()->getId())
                ->setParameter('provincia', $usuario->getProvincia()->getId())
                ->getResult();
       // }
        return array_merge($query, $query1);
    }

    public function ObtenerTodas()
    {
        $query = $this->createQueryBuilder('u')
            ->andWhere('u.fecha_leido is null')
            ->getQuery()->getResult();
        return $query;
    }

}
