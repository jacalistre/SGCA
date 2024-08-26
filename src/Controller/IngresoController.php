<?php

namespace App\Controller;

use App\Entity\Cama;
use App\Entity\Centro;
use App\Entity\EstadoCama;
use App\Entity\Ingreso;
use App\Entity\Notificacion;
use App\Entity\Sala;
use App\Form\IngresoType;
use App\Repository\IngresoRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ingreso")
 */
class IngresoController extends AbstractController
{
    /**
     * @Route("/", name="ingreso_index", methods={"GET"})
     */
    public function index(IngresoRepository $ingresoRepository): Response
    {
        return $this->render('ingreso/index.html.twig', [
            'ingresos' => $ingresoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ingreso_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ingreso = new Ingreso();
        $form = $this->createForm(IngresoType::class, $ingreso);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingreso);
            $entityManager->flush();

            return $this->redirectToRoute('ingreso_index');
        }

        return $this->render('ingreso/new.html.twig', [
            'ingreso' => $ingreso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ingreso_show", methods={"GET"})
     */
    public function show(Ingreso $ingreso): Response
    {
        return $this->render('ingreso/show.html.twig', [
            'ingreso' => $ingreso,
        ]);
    }

   /**
     * @Route("/{id}/edit", name="ingreso_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ingreso $ingreso): Response
    {
        $form = $this->createForm(IngresoType::class, $ingreso);

        $entityManager=$this->getDoctrine()->getManager();
        $centros=[];
        if($this->getUser()->getRoles()=="ROLE_COORDINADOR_MUNICIPAL"){
            $centros= $entityManager->getRepository(Centro::class)->findCentrosRol($this->getUser()->getProvincia()->getId(),$this->getUser()->getMunicipio()->getId());
        }else   if($this->getUser()->getRoles()=="ROLE_COORDINADOR_PROVINCIAL") {
            $centros= $entityManager->getRepository(Centro::class)->findCentrosRol($this->getUser()->getProvincia()->getId(),null);
        }
        $form->remove("centro")
        ->add('centro', EntityType::class, ['class'=>Centro::class,'choice_label' => 'nombre','attr'=>['class'=>'selectpicker form-control centro'],'placeholder'=>'Centro',
        'required' => true,
        'multiple' => false,
        'expanded' => false,
        'choices'=>$centros
    ]);
        if($ingreso->getSala()==null){

            $form->add('sala', null, ['attr'=>['class'=>'selectpicker form-control sala'],'placeholder'=>'Sala',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>[]

            ]) ->add('cama', null, ['attr'=>['class'=>'selectpicker form-control cama'],'placeholder'=>'Cama',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>[]

            ]);
        }else{
          $camas=$ingreso->getSala()->CamasSinOcupar();
          $camas[]=$ingreso->getCama();
            $form->add('sala', EntityType::class, ['class'=>Sala::class,'choice_label' => 'nombre','attr'=>['class'=>'selectpicker form-control sala'],'placeholder'=>'Sala',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$ingreso->getCentro()->getSalas()

            ]) ->add('cama', EntityType::class, ['class'=>Cama::class,'choice_label' => 'numero','attr'=>['class'=>'selectpicker form-control cama'],'placeholder'=>'Cama',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$camas
            ]);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $peticion=$request->request->get("ingreso");
            $sala=  $this->getDoctrine()->getManager()->getRepository(Sala::class)->find($peticion['sala']);
            $centro=  $this->getDoctrine()->getManager()->getRepository(Centro::class)->find($peticion['centro']);
            $cama=  $this->getDoctrine()->getManager()->getRepository(Cama::class)->find($peticion['cama']);
            $cama->setEstado($this->getDoctrine()->getManager()->getRepository(EstadoCama::class)->Obtener("Ocupada"));
            $ingreso->setSala($sala);
            $ingreso->setCentro($centro);
            $ingreso->setCama($cama);
            $ingreso->setEstado("Pendiente");
            $this->getDoctrine()->getManager()->flush();
//NOTIFICAR AL QUE LO INGRESO PARA SU RECOGIDA

            if($this->getUser()->getRoles()=="ROLE_COORDINADOR_MUNICIPAL") {
                $notificacion = new Notificacion();
                $notificacion->setPaciente($ingreso->getPaciente());
                $notificacion->setDestino($ingreso->getUsuario()->getId());
                $notificacion->setOrigen($this->getUser());
                $notificacion->setFechaEnvio(new \DateTime("now"));
                $notificacion->setMensaje("Se le asigno ubicacion en " . $centro->getNombre() . " al paciente");
                $entityManager->persist($notificacion);
                $entityManager->flush();
            }else if($this->getUser()->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
                $notificacion = new Notificacion();
                $notificacion->setPaciente($ingreso->getPaciente());
                $notificacion->setDestino("ROLE_COORDINADOR_MUNICIPAL");
                $notificacion->setOrigen($this->getUser());
                $notificacion->setFechaEnvio(new \DateTime("now"));
                $notificacion->setMensaje("Se le asigno ubicacion en el hospital " . $centro->getNombre() . " al paciente");
                $entityManager->persist($notificacion);
                $entityManager->flush();
            }

//NOTIFICAR AL CENTRO DE SU POSIBLE ARRIBO
            foreach ($centro->getUsuarios() as $u) {
                $notice = new Notificacion();
                $notice->setPaciente($ingreso->getPaciente());
                $notice->setDestino($u->getId());
                $notice->setOrigen($this->getUser());
                $notice->setFechaEnvio(new \DateTime("now"));
                $notice->setMensaje("Se le asigno  a su centro el  paciente");
                $entityManager->persist($notice);
                $entityManager->flush();
            }
            return $this->redirectToRoute('paciente_index');
        }

        return $this->render('ingreso/edit.html.twig', [
            'ingreso' => $ingreso,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ingreso_delete", methods={"POST"})
     */
    public function delete(Request $request, Ingreso $ingreso): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingreso->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ingreso);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ingreso_index');
    }
}
