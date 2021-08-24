<?php

namespace App\Repository;

use App\Entity\Centro;
use App\Entity\Ingreso;
use App\Entity\Paciente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Paciente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paciente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paciente[]    findAll()
 * @method Paciente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PacienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paciente::class);
    }

    // /**
    //  * @return Paciente[] Returns an array of Paciente objects
    //  */

    public function ObtenerPacientesCentro($idcentro)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT distinct p.* FROM ingreso i  join paciente p on i.paciente_id=p.id WHERE i.centro_id=:val and (i.estado like 'Ingresado' or i.estado like 'Pendiente' or i.estado like '%Pendiente Hospital%' or i.estado like '%Pendiente Remision%' or ( i.estado like '%Alta%' and i.fecha_transportado is null) ) " ,$rsm)
   ->setParameter('val', $idcentro)
            ->getResult();

    }

    public function ObtenerPacientesIngresadorxUsuario($idusuario)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id WHERE i.usuario_id=:val" ,$rsm)
            ->setParameter('val', $idusuario)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxUsuario($idusuario,$fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.usuario_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
            ->setParameter('val', $idusuario)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }
    public function ObtenerPacientesEvolutivoIngresadorxArea($area,$fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.area_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
            ->setParameter('val', $area)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }
    public function ObtenerPacientesEvolutivoIngresadorxCentro($centro,$fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.centro_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
            ->setParameter('val', $centro)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxMunicipio($municipio,$fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.municipio_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
            ->setParameter('val', $municipio)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }
    public function ObtenerPacientesEvolutivoIngresadorxProvincia($provincia,$fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.provincia_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
            ->setParameter('val', $provincia)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult()
        ;
    }
    public function ObtenerPacientesEvolutivoIngresadorAll($fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.estado like :estado and r.resultado is null and fecha= :fecha" ,$rsm)
             ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }
    public function ObtenerPacientes($data,$centro_id,$paciente_id)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return         $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id  WHERE i.estado like :estado  and i.centro_id= :centro and p.id<> :pacienteid and (p.nombre like :coincidencia or p.apellidos like :coincidencia or p.carnet like :coincidencia or p.pasaporte like :coincidencia) limit 10" ,$rsm)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('coincidencia', '%'.$data.'%')
            ->setParameter('centro', $centro_id)
            ->setParameter('pacienteid', $paciente_id)
            ->getResult();

    }
   /* public function findOneBySomeField($value): ?Paciente
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
