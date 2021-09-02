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
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        /* if ($user->getRoles() == "ROLE_AREA") {
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
         } else if ($user->getRoles() == "ROLE_ADMIN" or $user->getRoles()=="ROLE_SUPER_ADMIN") {
             $pacientes = new ArrayCollection($pacienteRepository->findAll());
         }
 */
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
                return $entry->getIngresos() == null || $entry->getIngresos()->count() == 0;
            }
            if ($estado == "Alta clinica") {
                return $entry->isAltaClinica();
            }
            if ($estado == "Ingreso domicilio") {
                return $entry->isDomicilio();
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
        if (!$form->isSubmitted() and $this->getUser()->getRoles() == "ROLE_LABORATORIO") {
            $form->remove('epidemiologia')
                ->remove('sintomatologia')
                ->remove('riesgo')
                ->remove('transportable')
                ->remove('vacuna')
                ->remove('dosis')
                ->remove('hta')
                ->remove('dm')
                ->remove('epoc')
                ->remove('ab')
                ->remove('obeso')
                ->remove('ci')
                ->remove('vih')
                ->remove('trastornos')
                ->remove('inmunodeprimido')
                ->remove('transporte_sanitario')
                ->remove('cancer')
                ->remove('otros')
                ->remove('fc')->remove('fr')->remove('ta')->remove('saturacion');

        }
        if ($form->isSubmitted() && $form->isValid()) {
             if($paciente->getCarnet()==null && $paciente->getPasaporte()==null){
                 $this->addFlash("error", "El paciente debe tener CI o Pasaporte");
                 return $this->render('paciente/new.html.twig', [
                     'paciente' => $paciente,
                     'form' => $form->createView(),
                 ]);
             }
            $entityManager = $this->getDoctrine()->getManager();
            $p = $entityManager->getRepository(Paciente::class)->Coincide($paciente);
            if ($p != null) {
                $this->addFlash("error", "El paciente que ud esta intentando insertar cooincide con otro :" . $paciente->getNombre() . " " . $paciente->getApellidos() . " " . $paciente->getCarnet());
                return $this->render('paciente/new.html.twig', [
                    'paciente' => $paciente,
                    'form' => $form->createView(),
                ]);
            }
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
                $fechai=new \DateTime('now');
                
                $prueba->setFecha($fechai->add(new \DateInterval("P" . $days . "D")));
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
     * @Route("/datatable/server", name="data_demand", methods={"POST"})
     */
    public function serverdemand(Request $request): Response
    {
        $draw = $request->get("draw");
        $draw++;
        $columns = $request->get("columns");
        $order = $request->get("order");
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get("search");
        $filtro=$request->get("filtro");
        $sql_response = $this->getDoctrine()->getRepository(Paciente::class)->ObtenerPacientesRol($this->getUser(), $columns, $search['value'], $order, $start, $length,$filtro);
        $items = $sql_response["items"];
        $rol = $this->getUser()->getRoles();
        $data=[];
        foreach ($items as $i) {
            if($i==null) continue;
            $info = $i->toDataTable($rol);

            $acciones = "";
            if ($rol != "ROLE_AREA") {
                $info[0] = "<span class='" . $i->getColorEstado() . "'> </span><a href='" . $this->generateUrl('paciente_show', ['id' => $i->getId()]) . "'> " . $info[0] . "</a>";
            } else {
                $info[1] = "<span class='" . $i->getColorEstado() . "'> </span><a href='" . $this->generateUrl('paciente_show', ['id' => $i->getId()]) . "'> " . $info[1] . "</a>";
            }
          /*  if ($rol == 'ROLE_COORDINADOR_PROVINCIAL' && ($i->isRemision() || $i->isPendienteHospital())) {
                $acciones .= "<a  href='" . $this->generateUrl('rechazar_solicitud', ["id" => $i->getId()]) . "'
                                           title='Rechazar peticion'><span
                                                    class='fa fa-thumbs-down' style='color: darkred'></span></a>"; /*{# Rechazar  Peticion#}*/
       /*     }
            if ($rol == "ROLE_SUPER_ADMIN") {
                $acciones .= "<a href='" . $this->generateUrl('super_delete', ["id" => $i->getId()]) . "' onclick='return confirm(\"Estas seguro que deseas eliminar este paciente\");'
                                           title='Eliminar'><span
                                                    class='fa fa-trash'></span></a>  /*{#Eliminar pacientes, ingresos y pruebas#}*/
                    /*           ";
            }
            if ($rol == 'ROLE_AREA' || $rol == "ROLE_CENTRO" || $rol == "ROLE_HOSPITAL") {
                if ($i->getIngresoActual() == null) {
                    $acciones .= "<a href='" . $this->generateUrl('ingreso_domicilio', ['id' => $i->getId()]) . "' onclick='return confirm(\"Estas seguro que deseas enviar el paciente a ingreso domiciliario\");'
                                           title='Ingreso Domicilio'><span
                                                    class='fa fa-home'></span></a>";/* {#Ingreso Domicilio#} */
         /*       }
            }
            if ($rol == 'ROLE_LABORATORIO') {
                $acciones .= "<a onclick='PrepararPrueba(this)' href='#' data-paciente='" . $this->g . "'
                                           title='Registrar resultado de prueba'><span
                                                    class='fa fa-eyedropper'></span></a>"; /*{# Registrar Prueba#}*/
           /* }
            if ($rol == 'ROLE_CENTRO' || $rol == 'ROLE_HOSPITAL' || $rol == 'ROLE_ADMIN') {
                if ($i->isPendienteIngreso()) {
                    $acciones .= "<a href='" . $this->generateUrl("confirmar_ingreso", ["id" => $i->getIngresoActual()->getId()]) . "'
                                               title='Confirmar Ingreso'><span
                                                        class='fa fa-check'></span></a>"; /*{#Confirmar Ingreso#}*/

              /*  }
                if ($i->isIngresado()) {
                    if ($i->acompanante == null) {
                        $acciones .= " <a onclick='PrepararAcompannante(this)' href='#'
                                                   data-paciente='" . $i->getId() . "' title='Asignar acompañante'><span
                                                            class='fa fa-user-md'></span></a>"; /* {# Asignar acompañante#} */
             /*       }
                    if ($i->getIngresoActual()->cama != null) {
                        $acciones .= " <a onclick='PrepararPrueba(this)' href='#' data-paciente='" . $i->getId() . "'
                                               title='Registrar resultado de prueba'><span
                                                        class='fa fa-eyedropper'></span></a>"; /*{# Registrar Prueba#}*/
               /*         $acciones .= " <a href='" . $this->generateUrl('paciente_remision', ['id' => $i->getId()]) . "' onclick='return confirm(\"¿Estas seguro que deseas solicitar una remision del paciente?\")'
                                               title='Solicitar remision' style='color: red;'><span
                                                        class='fa fa-medkit'></span></a>";/* {#Solicitar remision#}*/
                    /*    $acciones .= "         <a href='" . $this->generateUrl("alta", ["id" => $i->getIngresoActual()->getId()]) . "' onclick='return confirm(\"¿Estas seguro que desea darle alta al paciente?, si el paciente no posee prueba negativa se procedera a alta clinica\")'
                                               title='Alta'><span class='fa fa-taxi'></span></a>";/* {#Alta#}*/
                  /*      $acciones .= "<a href='" . $this->generateUrl('paciente_fallecio', ["id" => $i->getIngresoActual()->getId()]) . "' onclick='return  confirm(\"Estas seguro que deseas reportar el paciente como fallecido\");'
                                               title='Alta por fallecimiento' style='color: black;'><span
                                                        class='fa fa-heart'></span></a>";/* {#Alta fallecimiento#}*/
               /*     }
                    $acciones .= "<a href='#' onclick='PrepararCambio(this)'
                                               title='Reubicar dentro del centro'
                                               data-ingreso='" . $i->getIngresoActual()->getId() . "'
                                               style='color: green';><span
                                                        class='fa fa-building'></span></a>"; /*{#Reubicar dentro del centro#}*/
/*
                }
                if (($i->isAlta() && $i->fueTransportado() != null) || ($i->isAltaClinica() && $i->fueTransportado() != null && $i->fueTransportado()->getCama() != null)) {
                    $acciones .= "<a href='" . $this->generateUrl('salida_centro', ["id" => $i->fueTransportado()->getId()]) . "'
                                               title='Confirmar salida del centro' style='color: green;'><span
                                                        class='fa fa-check-square-o'></span></a>"; /*{#Confirmar salida del centro#}*/
        /*        }

            }
            if ($rol == 'ROLE_AREA' && (($i->getIngresoActual() == null && !$i->isFallecido()) || $i->isPendienteUbic() || $i->isDomicilio())) {
                $acciones .= " <a href='" . $this->generateUrl('paciente_hospital', ["id" => $i->getId()]) . "'
                                           title='Solicitar ingreso a un hospital' onclick='return confirm(\"Estas seguro que deseas solicitar ingreso a un hospital del paciente\")'
                                           style='color: red;'><span
                                                    class='fa fa-hospital-o' ></span></a>"; /*{#Solicitar ingreso a un hospital#}*/
             /*   if ($i->isDomicilio()) {
                    $acciones .= "<a href='" . $this->generateUrl('alta', ["id" => $i->getIngresoActual()->getId()]) . "' onclick='return confirm(\"¿Estas seguro que desea darle alta al paciente?, si el paciente no posee prueba negativa se procedera a alta clinica\")'
                                                   title='Alta'><span class='fa fa-taxi'></span></a>"; /* {#Alta#}*/
              /*      $acciones .= "<a onclick='PrepararPrueba(this)' href='#' data-paciente='" . $i->getId() . "'    title='Registrar resultado de prueba'><span
                                                            class='fa fa-eyedropper'></span></a>";/* {# Registrar Prueba#}*/
             /*   }
            }
            if ($rol == 'ROLE_CENTRO' || $rol == 'ROLE_ADMIN' || $rol == 'ROLE_COORDINADOR_MUNICIPAL') {
                if ($i->isIngresado() || $i->isPendienteUbic()) {
                    $acciones .= "<a href='" . $this->generateUrl('paciente_hospital', ["id" => $i->getId()]) . "'
                                               title='Solicitar ingreso a un hospital' onclick='return confirm(\"Estas seguro que deseas solicitar ingreso a un hospital del paciente\")'

                                               style='color: red;'><span
                                                        class='fa fa-hospital-o'></span></a>"; /* {#Solicitar ingreso a un hospital#}*/

         /*       }
            }
            if ((($i->isAlta() && $i->fueTransportado() == null) || $i->getIngresos() == null || $i->getIngresos()->count() == 0 || $i->isAltaClinica()) && $rol == 'ROLE_AREA') {
                $acciones .= "<a href='#' onclick='PrepararIngreso(this)'
                                               data-paciente='" . $i->getId() . "' title='Solicitar Ingreso'><span
                                                        class='fa fa-ambulance'></span></a>"; /*{#Solicitar Ingreso#}*/
          /*  }
            if ($rol == 'ROLE_COORDINADOR_MUNICIPAL' || $rol == 'ROLE_COORDINADOR_PROVINCIAL' || $rol == 'ROLE_ADMIN') {
                if ($i->isPendienteIngreso()) {
                    $acciones .= "<a href='" . $this->generateUrl('retirar_ingreso', ["id" => $i->getIngresoActual()->getId()]) . "'
                                               title='Retirar Ubicacion' style='color: green;'><span
                                                        class='fa fa-user-times'></span></a>";/* {#Eliminar ingreso#}*/
          /*      }

                if ($i->isPendienteUbic() || $i->isRemision() || $i->isPendienteHospital()) {

                    $acciones .= "<a href='" . $this->generateUrl('paciente_edit', ["id" => $i->getId()]) . "'
                                               title='Ubicar' style='color: green;'><span
                                                        class='fa fa-street-view'></span></a>";/* {#Asignar una ubicacion#}*/
            /*    }
            }
            $acciones .= "<a title='Editar Paciente'
                                       href='" . $this->generateUrl('paciente_edit', ["id" => $i->getId()]) . "'><span
                                                class='fa fa-edit'></span></a>";
            if ($rol == 'ROLE_ADMIN') {
                $acciones .= $this->renderView('paciente/_delete_form.html.twig', ["paciente" => $i]);

            }
*/
            $acciones=$this->renderView('paciente/acciones.html.twig',["paciente"=>$i]);
            $info[count($info) - 1] = $acciones;
            $data[] = $info;
        }

        return new Response(json_encode(["data" => $data, "draw" => $draw, "recordsTotal" => $sql_response["recordsTotal"], "recordsFiltered" => $sql_response["recordsFiltered"]]));
    }
    /* <tr>
                                {% if is_granted('ROLE_AREA') %}

                                    <td>

                                        {% if paciente.getIngresoActual()!=null %}
                                        {{ paciente.getIngresoActual().fechaconfirmacion is empty ?'': paciente.getIngresoActual().fechaconfirmacion | date('m/d/Y')  }}
                                    {% endif %}

                                    </td>
                                {% endif %}
                                <td><span class="{{ paciente.getColorEstado }}"></span><a
                                            href="{{ path('paciente_show', {'id': paciente.id}) }}">{{ paciente.nombre }}</a>
                                </td>
                                <td>{{ paciente.apellidos }}</td>
                                <td>{{ paciente.carnet }}</td>
                                <td>{{ paciente.pasaporte }}</td>
                                <td>{{ paciente.edad }}</td>
                                <td>{{ paciente.riesgo }}</td>
                                <td>{{ paciente.provincia }}</td>
                                <td>{{ paciente.municipio }}</td>
                                <td>{{ paciente.area }}</td>
                                {% if is_granted('ROLE_AREA') %}
                                <td>{{ paciente.consultorio }}</td>

                                <td>{{ paciente.direccionres }}</td>
                                {% endif %}
                                <td>
                                    {% if paciente.isIngresado() or paciente.isPendienteIngreso() %}
                                        {{ paciente.getIngresoActual().centro }}/
                                        {{ paciente.getIngresoActual.sala }}/
                                        {{ paciente.getIngresoActual.cama }}
                                    {% endif %}
                                </td>
                                <td>{% if paciente.getTransporteSanitario %}
                                        SI
                                    {% else %}
                                        NO
                                    {% endif %}
                                </td>
                                {% if is_granted("ROLE_COORDINADOR_MUNICIPAL") or is_granted("ROLE_COORDINADOR_PROVINCIAL") %}
                               <td>{{  paciente.observaciones }}</td>
                                {% endif %}
                                <td>
                                    {% if is_granted('ROLE_COORDINADOR_PROVINCIAL') and (paciente.isRemision() or paciente.isPendienteHospital()) %}
                                        <a  href="{{ path('rechazar_solicitud',{'id':paciente.id}) }}"
                                           title="Rechazar peticion"><span
                                                    class="fa fa-thumbs-down" style="color: darkred"></span></a> {# Rechazar  Peticion#}
                                    {% endif %}
                                    {% if is_granted("ROLE_SUPER_ADMIN") %}
                                        <a href="{{ path('super_delete',{'id':paciente.id}) }}" onclick="return confirm('Estas seguro que deseas eliminar este paciente');"
                                           title="Eliminar"><span
                                                    class="fa fa-trash"></span></a> {#Eliminar pacientes, ingresos y pruebas#}
                                    {% endif %}
                                    {% if is_granted('ROLE_AREA') or is_granted("ROLE_CENTRO") or is_granted("ROLE_HOSPITAL") %}
                                    {% if paciente.getIngresoActual==null %}
                                        <a href="{{ path('ingreso_domicilio',{'id':paciente.id}) }}" onclick="return confirm('Estas seguro que deseas enviar el paciente a ingreso domiciliario');"
                                           title="Ingreso Domicilio"><span
                                                    class="fa fa-home"></span></a> {#Ingreso Domicilio#}
                                        {% endif %}
                                    {% endif %}
                                    {% if is_granted('ROLE_LABORATORIO') %}
                                        <a onclick="PrepararPrueba(this)" href="#" data-paciente="{{ paciente.id }}"
                                           title="Registrar resultado de prueba"><span
                                                    class="fa fa-eyedropper"></span></a> {# Registrar Prueba#}
                                    {% endif %}
                                    {% if is_granted('ROLE_CENTRO')  or is_granted('ROLE_HOSPITAL') or is_granted('ROLE_ADMIN') %}
                                        {% if paciente.isPendienteIngreso() %}
                                            <a href="{{ path('confirmar_ingreso',{'id':paciente.getIngresoActual.id}) }}"
                                               title="Confirmar Ingreso"><span
                                                        class="fa fa-check"></span></a> {#Confirmar Ingreso#}

                                        {% endif %}


                                        {% if paciente.isIngresado()  %}
                                            {% if paciente.acompanante==null %}
                                                <a onclick="PrepararAcompannante(this)" href="#"
                                                   data-paciente="{{ paciente.id }}" title="Asignar acompañante"><span
                                                            class="fa fa-user-md"></span></a> {# Asignar acompañante#}
                                            {% endif %}
                                          {% if paciente.getIngresoActual.cama is not null %}
                                            <a onclick="PrepararPrueba(this)" href="#" data-paciente="{{ paciente.id }}"
                                               title="Registrar resultado de prueba"><span
                                                        class="fa fa-eyedropper"></span></a> {# Registrar Prueba#}

                                            <a href="{{ path('paciente_remision',{'id':paciente.id}) }}" onclick="return confirm('¿Estas seguro que deseas solicitar una remision del paciente?')"
                                               title="Solicitar remision" style="color: red;"><span
                                                        class="fa fa-medkit"></span></a> {#Solicitar remision#}
                                            <a href="{{ path('alta',{'id':paciente.getIngresoActual.id}) }}" onclick="return confirm('¿Estas seguro que desea darle alta al paciente?, si el paciente no posee prueba negativa se procedera a alta clinica')"
                                               title="Alta"><span class="fa fa-taxi"></span></a> {#Alta#}
                                            <a href="{{ path('paciente_fallecio',{'id':paciente.getIngresoActual.id}) }}" onclick="return  confirm('Estas seguro que deseas reportar el paciente como fallecido');"
                                               title="Alta por fallecimiento" style="color: black;"><span
                                                        class="fa fa-heart"></span></a> {#Alta fallecimiento#}
                                           {% endif %}
                                            <a href="#" onclick="PrepararCambio(this)"
                                               title="Reubicar dentro del centro"
                                               data-ingreso="{{ paciente.getIngresoActual.id }}"
                                               style="color: green;"><span
                                                        class="fa fa-building"></span></a> {#Reubicar dentro del centro#}
                                        {% endif %}
                                        {% if((paciente.isAlta() and   paciente.fueTransportado()!=null) or (paciente.isAltaClinica() and paciente.fueTransportado()!=null and paciente.fueTransportado.cama!=null)) %}
                                            <a href="{{ path('salida_centro',{'id':paciente.fueTransportado().id}) }}"
                                               title="Confirmar salida del centro" style="color: green;"><span
                                                        class="fa fa-check-square-o"></span></a> {#Confirmar salida del centro#}
                                        {% endif %}
                                    {% endif %}
                                    {% if is_granted('ROLE_AREA') and( (paciente.getIngresoActual()==null and not paciente.isFallecido() ) or paciente.isPendienteUbic() or paciente.isDomicilio) %}


                                        <a href="{{ path('paciente_hospital', {'id': paciente.id}) }}"
                                           title="Solicitar ingreso a un hospital" onclick="return confirm('Estas seguro que deseas solicitar ingreso a un hospital del paciente')"
                                           style="color: red;"><span
                                                    class="fa fa-hospital-o" ></span></a> {#Solicitar ingreso a un hospital#}
                                            {% if paciente.isDomicilio %}
                                                <a href="{{ path('alta',{'id':paciente.getIngresoActual.id}) }}" onclick="return confirm('¿Estas seguro que desea darle alta al paciente?, si el paciente no posee prueba negativa se procedera a alta clinica')"
                                                   title="Alta"><span class="fa fa-taxi"></span></a> {#Alta#}
                                                <a onclick="PrepararPrueba(this)" href="#" data-paciente="{{ paciente.id }}"
                                                   title="Registrar resultado de prueba"><span
                                                            class="fa fa-eyedropper"></span></a> {# Registrar Prueba#}
                                                {% endif %}

                                    {% endif %}

                                    {% if is_granted('ROLE_CENTRO') or is_granted('ROLE_ADMIN') or  is_granted('ROLE_COORDINADOR_MUNICIPAL') %}
                                        {% if paciente.isIngresado() or paciente.isPendienteUbic() %}
                                            <a href="{{ path('paciente_hospital',{'id':paciente.id}) }}"
                                               title="Solicitar ingreso a un hospital" onclick="return confirm('Estas seguro que deseas solicitar ingreso a un hospital del paciente')"

                                               style="color: red;"><span
                                                        class="fa fa-hospital-o"></span></a> {#Solicitar ingreso a un hospital#}


                                        {% endif %}
                                    {% endif %}
                                        {% if ((paciente.isAlta and  paciente.fueTransportado()==null) or paciente.ingresos==null or paciente.ingresos.count==0 or paciente.isAltaClinica) and is_granted('ROLE_AREA') %}
                                            <a href="#" onclick="PrepararIngreso(this)"
                                               data-paciente="{{ paciente.id }}" title="Solicitar Ingreso"><span
                                                        class="fa fa-ambulance"></span></a> {#Solicitar Ingreso#}
                                        {% endif %}


                                    {% if is_granted('ROLE_COORDINADOR_MUNICIPAL') or is_granted('ROLE_COORDINADOR_PROVINCIAL') or is_granted('ROLE_ADMIN') %}
                                        {% if paciente.isPendienteIngreso() %}
                                            <a href="{{ path('retirar_ingreso',{'id':paciente.getIngresoActual.id}) }}"
                                               title="Retirar Ubicacion" style="color: green;"><span
                                                        class="fa fa-user-times"></span></a> {#Eliminar ingreso#}
                                        {% endif %}

                                        {% if paciente.isPendienteUbic() or paciente.isRemision() or paciente.isPendienteHospital() %}
                                            <a href="{{ path('ingreso_edit',{'id':paciente.getIngresoActual.id}) }}"
                                               title="Ubicar" style="color: green;"><span
                                                        class="fa fa-street-view"></span></a> {#Asignar una ubicacion#}
                                        {% endif %}

                                    {% endif %}

                                    <a title="Editar Paciente"
                                       href="{{ path('paciente_edit', {'id': paciente.id}) }}"><span
                                                class="fa fa-edit"></span></a>
                                    {% if is_granted('ROLE_ADMIN') %}
                                        {{ include('paciente/_delete_form.html.twig') }}
                                    {% endif %}
                                </td>
                            </tr>*/

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
