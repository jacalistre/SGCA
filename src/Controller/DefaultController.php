<?php

namespace App\Controller;

use App\Entity\AreaSalud;
use App\Entity\Cama;
use App\Entity\Centro;
use App\Entity\Configuracion;
use App\Entity\Consultorio;
use App\Entity\EstadoCama;
use App\Entity\Ingreso;
use App\Entity\Municipio;
use App\Entity\Notificacion;
use App\Entity\Paciente;
use App\Entity\Provincia;
use App\Entity\Prueba;
use App\Entity\Sala;
use App\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/dashboard", name="default")
     */
    public function index(): Response
    {
      /*  return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);*/
        if($this->getUser()->getRoles()=='ROLE_ADMIN_MUN'){
            return $this->redirectToRoute('centro_index');
        }
      return $this->redirectToRoute('notificacion_index');
    }

    /**
     *
     * @Route("/ingreso/{id}/{d}/{m}/{y}", name="ingreso_request", methods={"GET"})
     *
     */
    public function solicitaringreso(Paciente $paciente, $d, $m, $y): Response
    {
        if ($paciente->getIngresos()->count() == 0 || $paciente->isAlta()) {
            $entityManager = $this->getDoctrine()->getManager();
            $ingreso = new Ingreso();
            $fecha = new \DateTime($y . '-' . $m . '-' . $d . " 00:00:00");
            $ingreso->setFechaConfirmacion($fecha);
            $ingreso->setPaciente($paciente);
            $ingreso->setEstado("Pendiente ubicacion");
            $ingreso->setUsuario($this->getUser());
            $entityManager->persist($ingreso);
            $entityManager->flush();

            $config = $entityManager->getRepository(Configuracion::class)->findAll();
            $days = ($config == null || count($config) == 0 ? 5 : $config[0]->getRotacionEvolutivo());


            $prueba = new Prueba();
            $prueba->setPaciente($paciente);
            $prueba->setIngreso($ingreso);
            $prueba->setFecha($fecha->add(new \DateInterval("P" . $days . "D")));

            $entityManager->persist($prueba);
            $entityManager->flush();

                $notificacion = new Notificacion();
                $notificacion->setPaciente($paciente);
                $notificacion->setDestino("ROLE_COORDINADOR_MUNICIPAL");
                $notificacion->setOrigen($this->getUser());
                $notificacion->setFechaEnvio(new \DateTime("now"));
                $notificacion->setMensaje("Se solicita ingreso  para el paciente ");
                $entityManager->persist($notificacion);
                $entityManager->flush();



        } else {
            $this->addFlash("error", "No se puede solictar ingreso para un paciente que esta actualmente ingresado");
        }
        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/ajaxsalas/{id}", name="getsalas", methods={"GET"})
     */
    public function findsala(Centro $centro): Response
    {
        return new JsonResponse($centro->SalaJson());
    }
    /**
     * @Route("/ajaxmunicipios/{id}", name="getmunicipios", methods={"GET"})
     */
    public function findMunicipios(Provincia $provincia): Response
    {
        return new JsonResponse($provincia->MunicipiosJson());
    }

    /**
     * @Route("/ajaxpacientes", name="getpacientes", methods={"POST"})
     */
    public function findPacientes(Request $request): Response
    {
        $data=$request->request->get("busqueda");
        $pacienteid=$request->request->get("paciente");
        $centro_id=$this->getUser()->getCentro()->getId();

        $pacientesRepository= $this->getDoctrine()->getRepository(Paciente::class);
        $pacientes= $pacientesRepository->ObtenerPacientes($data,$centro_id,$pacienteid);
        $json=[];
        foreach ($pacientes as $p){
            $json[]=["id"=>$p->getId(),"text"=>$p->getNombre()." ".$p->getApellidos()];
        }
      return new JsonResponse(["results"=>$json]);
    }
    /**
     * @Route("/ajaxarea/{id}", name="getareas", methods={"GET"})
     */
    public function findAreas(Municipio $municipio): Response
    {
        return new JsonResponse($municipio->AreasJson());
    }

    /**
     * @Route("/ajaxconsultorio/{id}", name="getconsultorios", methods={"GET"})
     */
    public function findConsultorios(AreaSalud $area): Response
    {
        return new JsonResponse($area->ConsultorioJson());
    }
    /**
     * @Route("/ajaxcentros/{id}", name="getcentros", methods={"GET"})
     */
    public function findCentros(Municipio $municipio): Response
    {
        return new JsonResponse($municipio->CentroJson());
    }

    /**
     * @Route("/ajaxcamas/{id}", name="getcamas", methods={"GET"})
     */
    public function findcamas(Sala $sala): Response
    {
        return new JsonResponse($sala->CamaJson());
    }

    /**
     * @Route("/retirar/{id}", name="retirar_ingreso", methods={"GET"})
     */
    public function retiraringreso(Ingreso $ingreso): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $cama = $ingreso->getCama();
        $estadoc = $entityManager->getRepository(EstadoCama::class)->Obtener("Disponible");
        $cama->setEstado($estadoc);
        $entityManager->flush();

        $ingreso->setEstado("Pendiente ubicacion");
        $ingreso->setCentro(null);
        $ingreso->setSala(null);

        $ingreso->setCama(null);

        $entityManager->flush();


        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/confirmar/{id}", name="confirmar_ingreso", methods={"GET"})
     */
    public function confirmaringreso(Ingreso $ingreso): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cama = $ingreso->getCama();
        $estadoc = $entityManager->getRepository(EstadoCama::class)->Obtener("Ocupada");
        $cama->setEstado($estadoc);
        $entityManager->flush();

        $ingreso->setEstado("Ingresado");
        $ingreso->setFechaEntrada(new \DateTime("now"));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return $this->redirectToRoute('paciente_index');
    }


    /**
     * @Route("/alta/{id}", name="alta", methods={"GET"})
     */
    public function alta(Ingreso $ingreso): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $evolutivo = $entityManager->getRepository(Prueba::class)->ObtenerEvolutivo($ingreso->getId());;
        if ($evolutivo == null) {
            /*  $cama = $ingreso->getCama();
              $estadoc = $entityManager->getRepository(EstadoCama::class)->Obtener("Disponible");
              $cama->setEstado($estadoc);
              $entityManager->flush();*/
            $ingreso->setEstado("Alta");
            $ingreso->setFechaAlta(new \DateTime("now"));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else {
            $this->addFlash("error", "Debe registrar al evolutivo pendiente su resultado para poder dar Alta");
        }
        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/prueba/{id}/{resultado}/{tipo}/{d}/{m}/{y}", name="registrar_prueba", methods={"GET"})
     */
    public function registrarprueba(Paciente $paciente, string $resultado, string $tipo, $d, $m, $y): Response
    {
        $fecha = new \DateTime($y . '-' . $m . '-' . $d . " 00:00:00");
        $entityManager = $this->getDoctrine()->getManager();
        $ingreso = $paciente->getIngresoActual();
        $prueba = null;
        $hoy = new \DateTime("now");
        if ($ingreso == null) {
            $prueba = new Prueba();
            $prueba->setPaciente($paciente);
            $prueba->setFecha($fecha);
            if ($resultado == "Positivo") {
                $this->addFlash("success", "Se recomienda ingreso del paciente");
            }
        } else {
            $prueba = $entityManager->getRepository(Prueba::class)->ObtenerEvolutivo($ingreso->getId());
            if($prueba==null){
                $this->addFlash("error","Ya el paciente en el ingreso actual posee una prueba negativa, proceda al alta del paciente");
                return $this->redirectToRoute('paciente_index');

            }
            if ($resultado == "Negativo") {
                $this->addFlash("success", "Proceda a realizar una  evaluacion clinica para el alta del paciente");
            } else {
                $evol = new Prueba();
                $evol->setFecha($fecha->add(new \DateInterval("P2D")));
                $evol->setPaciente($paciente);
                $evol->setIngreso($ingreso);

                $entityManager->persist($evol);
                $entityManager->flush();
                $this->addFlash("success", "Se le planifico un evolutivo en 48 horas al paciente a partir de la fecha de confirmacion");
            }
        }
        $prueba->setFechaNotificacion($hoy);
        $prueba->setResultado($resultado);
        $prueba->setTipo(($tipo == "PCR" ? "PCR" : "Test rapido"));
        $prueba->setFecha($fecha);
        if ($ingreso == null) {
            $entityManager->persist($prueba);
        }
        $entityManager->flush();
        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/transportado/{id}", name="salida_centro", methods={"GET"})
     */
    public function verificarsalida(Ingreso $ingreso): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($ingreso->getPaciente()->isAlta()) {
            $cama = $ingreso->getCama();
            $estadoc = $entityManager->getRepository(EstadoCama::class)->Obtener("Disponible");
            $cama->setEstado($estadoc);
            $entityManager->flush();
            $ingreso->setFechaTransportado(new \DateTime("now"));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else {
            $this->addFlash("error", "No se puede transportar un paciente si no esta de alta");
        }

        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/fallecio/{id}", name="paciente_fallecio", methods={"GET"})
     */
    public function fallecio(Ingreso $ingreso): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cama = $ingreso->getCama();
        if($cama!=null) {
            $estadoc = $entityManager->getRepository(EstadoCama::class)->Obtener("Disponible");
            $cama->setEstado($estadoc);
            $entityManager->flush();
        }
            $ingreso->setEstado("Fallecido");
            $ingreso->setFechaAlta(new \DateTime("now"));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $this->addFlash("success", "El paciente ha sido reportado como fallecido");

        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/reubicar/{id}/{centro}/{sala}/{cama}", name="paciente_reubicar", methods={"GET"})
     */
    public function reubicar(Ingreso $ingreso,Centro $centro,Sala $sala,Cama $cama): Response
    {     $entityManager = $this->getDoctrine()->getManager();
          $ingreso->setCentro($centro);
          $ingreso->setSala($sala);
          $estado=$entityManager->getRepository(EstadoCama::class)->Obtener("Ocupada");
          $cama->setEstado($estado);
          $ingreso->setCama($cama);
          $ingreso->setEstado("Pendiente");
          $entityManager->flush();
          if($centro->getId()!=$this->getUser()->getCentro()->getId()){
             foreach ($centro->getUsuarios() as $u) {
                 $n = new Notificacion();
                 $n->setPaciente($ingreso->getPaciente());
                 $n->setOrigen($this->getUser());
                 $n->setFechaEnvio(new \DateTime("now"));
                 $n->setMensaje("Se le ubico en su centro el paciente ");
                 $n->setDestino($u->getId());
                 $entityManager->persist($n);
                 $entityManager->flush();
                  }
          }
          $this->addFlash("success","Paciente reubicado satisfactoriamente");
        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/hospital/{id}", name="paciente_hospital", methods={"GET"})
     */
    public function sendhospital(Paciente $paciente): Response
    {        $entityManager= $this->getDoctrine()->getManager();


        if($paciente->getIngresoActual()==null){
            $ingreso= new Ingreso();
            $ingreso->setEstado("Pendiente Hospital");
            $ingreso->setPaciente($paciente);
            $ingreso->setFechaConfirmacion(new \DateTime('now'));
            $ingreso->setUsuario($this->getUser());
            $entityManager->persist($ingreso);
            $entityManager->flush();

        }else {

            $ingreso = $paciente->getIngresoActual();
            $ingreso->setEstado("Pendiente Hospital");
            $entityManager->flush();
        }

        $notificacion = new Notificacion();
        $notificacion->setPaciente($paciente);
        $notificacion->setDestino("ROLE_COORDINADOR_PROVINCIAL");
        $notificacion->setOrigen($this->getUser());
        $notificacion->setFechaEnvio(new \DateTime("now"));
        $notificacion->setMensaje("Se solicita ingreso en un hospital  para el paciente ");
        $entityManager->persist($notificacion);
        $entityManager->flush();

        $not= new Notificacion();
        $not->setPaciente($paciente);
        $not->setDestino("ROLE_COORDINADOR_MUNICIPAL");
        $not->setOrigen($this->getUser());
        $not->setFechaEnvio(new \DateTime("now"));
        $not->setMensaje("Se solicita ingreso en un hospital  para el paciente ");
        $entityManager->persist($not);
        $entityManager->flush();

        return $this->redirectToRoute('paciente_index');
    }

    /**
     * @Route("/remision/{id}", name="paciente_remision", methods={"GET"})
     */
    public function sendremision(Paciente $paciente): Response
    {
        $entityManager= $this->getDoctrine()->getManager();

         if($paciente->getIngresoActual()!=null){
            $ingreso= $paciente->getIngresoActual();
            $ingreso->setEstado("Pendiente Remision");
            $entityManager->flush();
         }


        $notificacion = new Notificacion();
        $notificacion->setPaciente($paciente);
        $notificacion->setDestino("ROLE_COORDINADOR_MUNICIPAL");
        $notificacion->setOrigen($this->getUser());
        $notificacion->setFechaEnvio(new \DateTime("now"));
        $notificacion->setMensaje("Se solicita remision para otro centro,  para el paciente ");

        $entityManager->persist($notificacion);
        $entityManager->flush();
        return $this->redirectToRoute('paciente_index');
    }


    /**
     * @Route("/evolutivo/{d}/{m}/{y}", name="paciente_evolutivo", methods={"GET"})
     */
    public function evolutivo($d,$m,$y): Response
    {   $pacienteRepository= $this->getDoctrine()->getRepository(Paciente::class);
        $user=$this->getUser();
        $pacientes=[];
        $fecha = new \DateTime($y . '-' . $m . '-' . $d . " 00:00:00");
        if($user->getRoles()=="ROLE_AREA"){
            $pacientes= new ArrayCollection(
                array_unique(
                    array_merge(
                        $pacienteRepository-> ObtenerPacientesEvolutivoIngresadorxUsuario($user->getId(),$fecha),
                        $pacienteRepository->ObtenerPacientesEvolutivoIngresadorxArea($user->getArea()->getId(),$fecha)))
            );

        }else if($user->getRoles()=="ROLE_CENTRO" || $user->getRoles()=="ROLE_HOSPITAL"){
            $pacientes=$pacienteRepository->ObtenerPacientesEvolutivoIngresadorxCentro($user->getCentro()->getId(),$fecha);

        }else if($user->getRoles()=="ROLE_COORDINADOR_MUNICIPAL"){
            $pacientes= $pacienteRepository->ObtenerPacientesEvolutivoIngresadorxMunicipio($user->getMunicipio()->getId(),$fecha);
        }else if($user->getRoles()=="ROLE_COORDINADOR_PROVINCIAL" || $user->getRoles()=="ROLE_LABORATORIO" ){
            $pacientes= $pacienteRepository->ObtenerPacientesEvolutivoIngresadorxProvincia($user->getProvincia()->getId(),$fecha);

        }else if($user->getRoles()=="ROLE_ADMIN"){
            $pacientes=$pacienteRepository->ObtenerPacientesEvolutivoIngresadorAll($fecha);
        }

        return $this->render('default/evolutivo.html.twig', [
            'pacientes' => $pacientes,
        ]);
    }
    /**
     * @Route("/acompanante/{paciente}/{estrategia}/{acompanante}", name="paciente_acompanante", methods={"GET"})
     */
    public function acompanante(Paciente $paciente,$estrategia,Paciente $acompanante): Response
    {
        $entityManager=$this->getDoctrine()->getManager();

        $ingresop=$paciente->getIngresoActual();
        $ingresoa=$acompanante->getIngresoActual();

        $paciente->setAcompanante($acompanante);
        if($estrategia==1){
            if($ingresop->getSala()==null){
                $this->addFlash("error","No se puede utilizar la estrategia seleccionada para asignar el acompañante");
                return $this->redirectToRoute('paciente_index');

            }else{

                $ingresoa->setSala($ingresop->getSala());
                $ingresoa->setCama($ingresop->getCama());
            }
        }else if($estrategia==2){
            if($ingresoa->getSala()==null) {
                $this->addFlash("error", "No se puede utilizar la estrategia seleccionada para asignar el acompañante");
                return $this->redirectToRoute('paciente_index');
            }else{
                $ingresop->setSala($ingresoa->getSala());
                $ingresop->setCama($ingresoa->getCama());
            }

        }
        $entityManager->flush();
        return $this->redirectToRoute('paciente_index');
    }
}
