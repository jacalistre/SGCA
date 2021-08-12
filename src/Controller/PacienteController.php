<?php

namespace App\Controller;

use App\Entity\AreaSalud;
use App\Entity\Configuracion;
use App\Entity\Consultorio;
use App\Entity\Ingreso;
use App\Entity\Paciente;
use App\Entity\Prueba;
use App\Form\PacienteType;
use App\Repository\PacienteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/paciente")
 */
class PacienteController extends AbstractController
{
    /**
     * @Route("/", name="paciente_index", methods={"GET"})
     */
    public function index(PacienteRepository $pacienteRepository): Response
    {
        $user = $this->getUser();
        $pacientes = new ArrayCollection();
        if ($user->getRoles() == "ROLE_AREA") {
            $pacientes = new ArrayCollection(
                array_unique(
                    array_merge(
                        $pacienteRepository->ObtenerPacientesIngresadorxUsuario($user->getId()),
                        $user->getArea()->getPacientes()->toArray()))
            );

        } else if ($user->getRoles() == "ROLE_CENTRO" || $user->getRoles() == "ROLE_HOSPITAL") {
            $pacientes = new ArrayCollection($pacienteRepository->ObtenerPacientesCentro($user->getCentro()->getId()));

        } else if ($user->getRoles() == "ROLE_COORDINADOR_MUNICIPAL") {
            $pacientes = $user->getMunicipio()->getPacientes();
        } else if ($user->getRoles() == "ROLE_COORDINADOR_PROVINCIAL" || $user->getRoles() == "ROLE_LABORATORIO") {
            $pacientes = $user->getProvincia()->getPacientes();
        } else if ($user->getRoles() == "ROLE_ADMIN") {
            $pacientes = new ArrayCollection($pacienteRepository->findAll());
        }

        return $this->render('paciente/index.html.twig', [
            'pacientes' => $pacientes,
        ]);
    }

    /**
     * @Route("/filter/{estado}", name="paciente_filter_index", methods={"GET"})
     */
    public function filterindex(string $estado): Response
    {
        $user = $this->getUser();
        $pacientes = new ArrayCollection();
        $pacienteRepository = $this->getDoctrine()->getRepository(Paciente::class);
        if ($user->getRoles() == "ROLE_AREA") {
            $pacientes = new ArrayCollection(
                array_unique(
                    array_merge(
                        $pacienteRepository->ObtenerPacientesIngresadorxUsuario($user->getId()),
                        $user->getArea()->getPacientes()->toArray()))
            );


        } else if ($user->getRoles() == "ROLE_CENTRO" || $user->getRoles() == "ROLE_HOSPITAL") {
            $pacientes = new ArrayCollection($pacienteRepository->ObtenerPacientesCentro($user->getCentro()->getId()));

        } else if ($user->getRoles() == "ROLE_COORDINADOR_MUNICIPAL") {
            $pacientes = $user->getMunicipio()->getPacientes();
        } else if ($user->getRoles() == "ROLE_COORDINADOR_PROVINCIAL" || $user->getRoles() == "ROLE_LABORATORIO") {
            $pacientes = $user->getProvincia()->getPacientes();
        } else if ($user->getRoles() == "ROLE_ADMIN") {
            $pacientes = new ArrayCollection($pacienteRepository->findAll());
        }
        $pacientes = $pacientes->filter(function ($entry) use ($estado) {
            if ($estado == "Ingresado") {
                return $entry->isIngresado();
            }
            if ($estado == "Pendiente") {
                return $entry->isPendienteIngreso();
            }
            if ($estado == "Pendiente ubicacion") {
                return $entry->isPendienteUbic();
            }
            if ($estado == "Alta") {
                return $entry->isAlta();
            }
            if ($estado == "Fallecido") {
                return $entry->isFallecido();
            }
            if ($estado == "Pendiente Hospital") {
                return $entry->isPendienteHospital();
            }
            if ($estado == "Pendiente Remision") {
                return $entry->isRemision();
            }
            if ($estado == "Sin ingresar") {
                return $entry->getIngresos()==null ||$entry->getIngresos()->count()==0 ;
            }
            return false;
        });


        return $this->render('paciente/index.html.twig', [
            'pacientes' => $pacientes, 'filtro' => true
        ]);
    }

    /**
     * @Route("/new", name="paciente_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $paciente = new Paciente();
        $form = $this->createForm(PacienteType::class, $paciente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $area = $entityManager->getRepository(AreaSalud::class)->find($request->request->get('paciente')['area']);
            $consultorio = $entityManager->getRepository(Consultorio::class)->find($request->request->get('paciente')['consultorio']);
            $paciente->setArea($area);
            $paciente->setConsultorio($consultorio);
            if ($this->getUser()->getRoles() == "ROLE_HOSPITAL") {
                $ingreso = new Ingreso();
                $ingreso->setUsuario($this->getUser());
                $ingreso->setCentro($this->getUser()->getCentro());
                $fecha = new \DateTime('now');
                $ingreso->setFechaConfirmacion($fecha);
                $ingreso->setEstado("Ingresado");
                $ingreso->setFechaEntrada(new \DateTime('now'));
                $paciente->addIngreso($ingreso);

                $config = $entityManager->getRepository(Configuracion::class)->findAll();
                $days = ($config == null || count($config) == 0 ? 5 : $config[0]->getRotacionEvolutivo());

                $prueba = new Prueba();
                $prueba->setFecha($fecha->add(new \DateInterval("P" . $days . "D")));
                $ingreso->addPrueba($prueba);
                $prueba->setPaciente($paciente);
            }
            $entityManager->persist($paciente);
            $entityManager->flush();
            return $this->redirectToRoute('paciente_index');
        }

        return $this->render('paciente/new.html.twig', [
            'paciente' => $paciente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="paciente_show", methods={"GET"})
     */
    public function show(Paciente $paciente): Response
    {
        return $this->render('paciente/show.html.twig', [
            'paciente' => $paciente,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="paciente_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Paciente $paciente): Response
    {
        $form = $this->createForm(PacienteType::class, $paciente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $area = $entityManager->getRepository(AreaSalud::class)->find($request->request->get('paciente')['area']);
            $consultorio = $entityManager->getRepository(Consultorio::class)->find($request->request->get('paciente')['consultorio']);
            $paciente->setArea($area);
            $paciente->setConsultorio($consultorio);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('paciente_index');
        }

        return $this->render('paciente/edit.html.twig', [
            'paciente' => $paciente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="paciente_delete", methods={"POST"})
     */
    public function delete(Request $request, Paciente $paciente): Response
    {
        if ($this->isCsrfTokenValid('delete' . $paciente->getId(), $request->request->get('_token'))) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($paciente);
                $entityManager->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash("error", "El paciente no se puede eliminar debido a que ya fue aceptado como valido en el sistema");
            }
        }

        return $this->redirectToRoute('paciente_index');
    }
}
