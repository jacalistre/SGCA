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
        return $this->getEntityManager()->createNativeQuery("SELECT distinct p.* FROM ingreso i  join paciente p on i.paciente_id=p.id WHERE i.centro_id=:val and (i.estado like 'Ingresado' or i.estado like 'Pendiente' or i.estado like '%Pendiente Hospital%' or i.estado like '%Pendiente Remision%' or ( i.estado like '%Alta%' and i.fecha_transportado is null) ) ", $rsm)
            ->setParameter('val', $idcentro)
            ->getResult();

    }

    public function Coincide($paciente)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM paciente p where p.carnet like :carnet and (p.nombre like :nombre or p.apellidos like :apellidos ) limit 1", $rsm)
            ->setParameter('nombre', $paciente->getNombre())
            ->setParameter('carnet', $paciente->getCarnet())
            ->setParameter('apellidos', $paciente->getApellidos())
            ->getOneOrNullResult();

    }

    public function ObtenerPacientesIngresadorxUsuario($idusuario)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id WHERE i.usuario_id=:val", $rsm)
            ->setParameter('val', $idusuario)
            ->getResult();

    }

    public function ObtenerPacientesRol($usuario, $columns, $general_search, $order, $start, $length,$filtro)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');/*where i.estado not like '%alta%' and i.estado not like '%fallecido%'*/
     /*   $sql = "Select  p.* from paciente p inner join municipio m on p.municipio_id=m.id inner join provincia r on r.id=p.provincia_id inner join area_salud a on a.id=p.area_id inner join consultorio l on p.consultorio_id=l.id left join (Select i.*,c.nombre,s.nombre as salan,k.numero from ingreso i left join centro c on i.centro_id=c.id left join sala s on i.sala_id=s.id left join cama k on k.id=i.cama_id  order by i.id desc limit 1) as ig on ig.paciente_id=p.id ";
        $sqlcount = "Select count(p.id) from paciente p inner join municipio m on p.municipio_id=m.id inner join provincia r on r.id=p.provincia_id inner join area_salud a on a.id=p.area_id inner join consultorio l on p.consultorio_id=l.id left join (Select i.*,c.nombre,s.nombre as salan,k.numero from ingreso i left join centro c on i.centro_id=c.id left join sala s on i.sala_id=s.id left join cama k on k.id=i.cama_id  order by i.id desc limit 1) as ig on ig.paciente_id=p.id ";

       */
        $sql="Select p.* from paciente p inner join municipio m on p.municipio_id=m.id inner join provincia r on p.provincia_id=r.id inner join area_salud a on p.area_id=a.id inner join consultorio n on p.consultorio_id=n.id left join (Select i.*,c.nombre,s.nombre as salan,c2.numero from ingreso i inner join (Select  max(i.id) as max from ingreso i group by paciente_id) ing  on i.id=ing.max left join centro c on i.centro_id = c.id left join sala s on i.sala_id = s.id left join cama c2 on i.cama_id = c2.id) ig on ig.paciente_id=p.id ";
        $sqlcount="Select count(p.id) as cant from paciente p inner join municipio m on p.municipio_id=m.id inner join provincia r on p.provincia_id=r.id inner join area_salud a on p.area_id=a.id inner join consultorio n on p.consultorio_id=n.id left join (Select i.*,c.nombre,s.nombre as salan,c2.numero from ingreso i inner join (Select  max(i.id) as max from ingreso i group by paciente_id) ing  on i.id=ing.max left join centro c on i.centro_id = c.id left join sala s on i.sala_id = s.id left join cama c2 on i.cama_id = c2.id) ig on ig.paciente_id=p.id ";

        $parametros = [];
        $sqlfilt="";
        if($filtro!=""){
            if($filtro=="Sin Ingresar"){
                $sqlfilt.=" and ( ig.estado is null and (Select count(im.id) from ingreso im where p.id=im.paciente_id) =0) ";
            }else {
                $sqlfilt = " AND ig.estado like :filtro";
                $parametros["filtro"] = "" . $filtro . "";
            }
        }
        if ($usuario->getRoles() == "ROLE_AREA") {
            // $sql .= "inner join ingreso i on p.id=i.paciente_id where (i.usuario_id=:val or p.area_id=:val1) ";
            $sql .= " where (ig.usuario_id= :valf or p.area_id=:n ) ";

            // $sqlcount .= "inner join ingreso i on p.id=i.paciente_id where (i.usuario_id=:val or p.area_id=:val1) ";
            $sqlcount .= " where ( ig.usuario_id= :valf or p.area_id= :n ) ";

            $parametros["valf"] = $usuario->getId();
            $parametros["n"] = $usuario->getArea()->getId();

        } else if ($usuario->getRoles() == "ROLE_CENTRO" || $usuario->getRoles() == "ROLE_HOSPITAL") {
            //  $sql .= "inner join ingreso i on p.id=i.paciente_id WHERE (i.centro_id=:val and (i.estado like 'Ingresado' or i.estado like 'Pendiente' or i.estado like '%Pendiente Hospital%' or i.estado like '%Pendiente Remision%' or ( i.estado like '%Alta%' and i.fecha_transportado is null))";
            $sql .= " WHERE ig.centro_id=:val and (ig.estado like 'Ingresado' or ig.estado like 'Pendiente' or ig.estado like '%Pendiente Hospital%' or ig.estado like '%Pendiente Remision%' or ( ig.estado like '%Alta%' and ig.fecha_transportado is null)) ";

            //  $sqlcount .= "inner join ingreso i on p.id=i.paciente_id WHERE (i.centro_id=:val and (i.estado like 'Ingresado' or i.estado like 'Pendiente' or i.estado like '%Pendiente Hospital%' or i.estado like '%Pendiente Remision%' or ( i.estado like '%Alta%' and i.fecha_transportado is null))";
            $sqlcount .= " WHERE ig.centro_id=:val and (ig.estado like 'Ingresado' or ig.estado like 'Pendiente' or ig.estado like '%Pendiente Hospital%' or ig.estado like '%Pendiente Remision%' or ( ig.estado like '%Alta%' and ig.fecha_transportado is null)) ";

            $parametros["val"] = $usuario->getCentro()->getId();
        } else if ($usuario->getRoles() == "ROLE_COORDINADOR_MUNICIPAL" or $usuario->getRoles()=="ROLE_ADMIN_MUNICIPAL") {
            // $sql .= "inner join ingreso i on p.id=i.paciente_id inner join usuario u on i.usuario_id= u.id where (p.municipio_id=:val or u.municipio_id=:val)";
            $sql .= " left join usuario u on ig.usuario_id= u.id where (p.municipio_id=:val or u.municipio_id=:val)";
            $sqlcount .= " left join usuario u on ig.usuario_id= u.id where (p.municipio_id=:val or u.municipio_id=:val) ";

            // $sqlcount .= "inner join ingreso i on p.id=i.paciente_id inner join usuario u on i.usuario_id= u.id where (p.municipio_id=:val or u.municipio_id=:val)";
            $parametros["val"] = $usuario->getMunicipio()->getId();
        } else if ($usuario->getRoles() == "ROLE_COORDINADOR_PROVINCIAL" || $usuario->getRoles() == "ROLE_LABORATORIO") {
            //$sql .= "inner join ingreso i on p.id=i.paciente_id inner join usuario u on i.usuario_id= u.id where (p.provincia_id=:val or u.provincia_id=:val)";
            $sql .= "left join usuario u on ig.usuario_id= u.id where (p.provincia_id=:val or u.provincia_id=:val) ";
            $sqlcount .= "left join usuario u on ig.usuario_id= u.id where (p.provincia_id=:val or u.provincia_id=:val) ";

            //$sqlcount .= "inner join ingreso i on p.id=i.paciente_id inner join usuario u on i.usuario_id= u.id where (p.provincia_id=:val or u.provincia_id=:val)";
            $parametros["val"] = $usuario->getProvincia()->getId();
        }
        $rsm1 = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm1->addEntityResult('App\Entity\Paciente', 'p');
        $rsm1->addFieldResult("p", "cant", "id");
        $recodsTotal = $this->getEntityManager()->createNativeQuery($sqlcount, $rsm1)->setParameters($parametros)->getResult();
$sql.=$sqlfilt;
$sqlcount.=$sqlfilt;

        $sqlFilter = "";
        //Busqueda general en todas las columnas OR
        if ($general_search != null) {
            $sqlFilter .= "(";

            foreach ($columns as $c) {
                if ($c['name'] == "acciones") {
                    continue;
                }
                if ($c['name'] == "provincia") {
                    $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " r.nombre  like :gsearch";
                } else
                    if ($c['name'] == "municipio") {
                        $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " m.nombre  like :gsearch";

                    } else
                        if ($c['name'] == "centro" ) {
                            $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " (ig.nombre  like :gsearch and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";

                        }  else
                            if ($c['name'] == "consultorio") {
                                $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " l.nombre  like :gsearch";

                            } else
                                if ($c['name'] == "direccion") {
                                    $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " (p.direccion_ci  like :gsearch or p.direccion_res like :qsearch )";

                                }  else
                            if ($c['name'] == "sala") {
                                $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " (ig.salan  like :gsearch and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";

                            } else
                                if ($c['name'] == "cama") {
                                    $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " (ig.numero  like :gsearch and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";

                                }else
                            if ($c['name'] == "area") {
                                $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " a.nombre  like :gsearch";

                            } else if ($c['name'] == "nombre") {
                                $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " p.nombre  like :gsearch";
                            } else {
                                $sqlFilter .= ($sqlFilter == "(" ? "" : " OR ") . " p.".$c['name'] . " like :gsearch";
                            }
            }
            $sqlFilter .= ")";
            $parametros ["gsearch"] = '%' . $general_search . '%';
        }
        //Busqueda especifica por columnas con AND
        $sqlFilterC = "";

        foreach ($columns as $c) {
            if ($c['name'] == "acciones") {
                continue;
            }
            if (!empty($c['search']['value'])) {
                if ($c['name'] == "provincia") {
                    $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " r.nombre like :" . $c['name'] . "";
                    $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                } else
                    if ($c['name'] == "nombre") {
                        $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " p.nombre like :" . $c['name'] . "";
                        $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                    } else
                        if ($c['name'] == "area") {
                            $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " a.nombre like :" . $c['name'] . "";
                            $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                        }  else
                            if ($c['name'] == "consultorio") {
                                $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " l.nombre like :" . $c['name'] . "";
                                $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                            } else
                                if ($c['name'] == "direccion") {
                                    $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " (p.direccion_ci like :" . $c['name'] . " or p.direccion_res like :" . $c['name'] . " )";
                                    $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                                }else
                            if ($c['name'] == "municipio") {
                                $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " m.nombre like :" . $c['name'] . "";
                                $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                            } else
                                if ($c['name'] == "centro") {
                                    $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " (ig.nombre like :" . $c['name'] . " and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";
                                    $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                                }else
                                    if ($c['name'] == "sala") {
                                        $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " (ig.salan like :" . $c['name'] . " and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";
                                        $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                                    }else
                                        if ($c['name'] == "cama") {
                                            $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") . " (ig.numero like :" . $c['name'] . " and ig.estado not like '%alta%' and ig.estado not like '%fallecido%')";
                                            $parametros[$c['name']] = "%" . $c['search']['value'] . "%";
                                        }else {
                                $sqlFilterC .= (empty($sqlFilterC) ? "" : " AND ") ." p.". $c['name'] . " like :" . $c['name'] . "";

                                $parametros[$c['name']] = "%" . $c['search']['value'] . "%";


                            }
            }
        }
        if (empty($sqlFilter)) {
            $sqlFilter .= $sqlFilterC;
        } else if (!empty($sqlFilterC)) {
            $sqlFilter .= " AND (" . $sqlFilterC . ")";
        }

        if (!empty($sqlFilter)) {
            if ($usuario->getRoles() == "ROLE_ADMIN" ) {
                $sql .= " where " . $sqlFilter.$sqlfilt;
                $sqlcount .= " where " . $sqlFilter.$sqlfilt;
            } else {
                $sql .= " AND (" . $sqlFilter . ")";
                $sqlcount .= " AND (" . $sqlFilter . ")";
            }

        }
        if(!empty($sqlfilt) && empty($sqlFilter) && $usuario->getRoles() == "ROLE_ADMIN" ){
            if($filtro=="Sin Ingresar"){
                $sql.=" where ig.estado is null and (Select count(im.id) from ingreso im where p.id=im.paciente_id) =0";
                $sqlcount.=" where ig.estado is null and (Select count(im.id) from ingreso im where p.id=im.paciente_id) =0";

            }else {
                $sql .= " where ig.estado like :filtro";
                $sqlcount .= " where ig.estado like :filtro";
                $parametros['filtro'] = "" . $filtro . "";
            }
        }

        //Ordenamientos
        $orders = "";
        foreach ($order as $o) {
            if ($columns[$o['column']]['name'] == "acciones") {
                continue;
            }
            if ($columns[$o['column']]['name'] == "provincia") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " r.nombre " . $o['dir'];

            } else if ($columns[$o['column']]['name'] == "nombre") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " p.nombre " . $o['dir'];

            } else if ($columns[$o['column']]['name'] == "area") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " a.nombre " . $o['dir'];

            } else if ($columns[$o['column']]['name'] == "municipio") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " m.nombre " . $o['dir'];

            }  else if ($columns[$o['column']]['name'] == "centro") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " ig.nombre " . $o['dir'];

            } else if ($columns[$o['column']]['name'] == "consultorio") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " l.nombre " . $o['dir'];

            }  else if ($columns[$o['column']]['name'] == "direccion") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " p.direccion_res,p.direccion_ci " . $o['dir'];

            }else if ($columns[$o['column']]['name'] == "sala") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " ig.salan " . $o['dir'];

            } else if ($columns[$o['column']]['name'] == "cama") {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " ig.numero " . $o['dir'];

            }else {
                $orders .= (empty($orders) ? " ORDER BY " : ",") . " p.".$columns[$o['column']]['name'] . " " . $o['dir'];

            }
        }
        if (!empty($orders)) {
            $sql .= $orders;
        }

            $sql .= " limit " . $start . "," . $length;

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');

        $items = $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters($parametros)->getResult();

         $recodsFiltered = $this->getEntityManager()->createNativeQuery($sqlcount, $rsm1)->setParameters($parametros)->getResult();

        return ["items" => $items, "recordsTotal" => $recodsTotal[0]->getId(), "recordsFiltered" => $recodsFiltered[0]->getId()];
    }


    public function ObtenerPacientesEvolutivoIngresadorxUsuario($idusuario, $fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.usuario_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('val', $idusuario)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxArea($area, $fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.area_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('val', $area)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxCentro($centro, $fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.centro_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('val', $centro)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxMunicipio($municipio, $fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.municipio_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('val', $municipio)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientesEvolutivoIngresadorxProvincia($provincia, $fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE p.provincia_id=:val and i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('val', $provincia)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();
    }

    public function ObtenerPacientesEvolutivoIngresadorAll($fecha)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id join prueba r on  i.id=r.ingreso_id WHERE i.estado like :estado and r.resultado is null and fecha= :fecha", $rsm)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('fecha', $fecha)
            ->getResult();

    }

    public function ObtenerPacientes($data, $centro_id, $paciente_id)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('App\Entity\Paciente', 'p');
        return $this->getEntityManager()->createNativeQuery("SELECT p.* FROM ingreso i  join paciente p on i.paciente_id=p.id  WHERE i.estado like :estado  and i.centro_id= :centro and p.id<> :pacienteid and (p.nombre like :coincidencia or p.apellidos like :coincidencia or p.carnet like :coincidencia or p.pasaporte like :coincidencia) limit 10", $rsm)
            ->setParameter('estado', '%Ingresado%')
            ->setParameter('coincidencia', '%' . $data . '%')
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
