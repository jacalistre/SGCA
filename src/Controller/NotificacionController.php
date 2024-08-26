<?php

namespace App\Controller;

use App\Entity\Notificacion;
use App\Form\NotificacionType;
use App\Repository\NotificacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/notificacion")
 */
class NotificacionController extends AbstractController
{
    /**
     * @Route("/", name="notificacion_index", methods={"GET"})
     */
    public function index(NotificacionRepository $notificacionRepository): Response
    {
        $user = $this->getUser();
        $notificacion = [];
        if ($user->getRoles() == "ROLE_ADMIN") {
            $notificacion = $notificacionRepository->findAll();
        } else {
            $notificacion = $notificacionRepository->ObtenerNotificaciones($user);
        }

        return $this->render('notificacion/index.html.twig', [
            'notificacions' => $notificacion
        ]);
    }
    /**
     * @Route("/getnotice", name="notificacion_get", methods={"GET","POST"})
     */
    public function getnotification(): Response
    {
        $user = $this->getUser();
        $notificacion = [];
        $notificacionRepository = $this->getDoctrine()->getRepository(Notificacion::class);
        if ($user->getRoles() == "ADMIN") {
            $notificacion = $notificacionRepository->ObtenerTodas();
        } else {
            $notificacion = $notificacionRepository->ObtenerNotificacionesSinLeer($user);
        }
        $c = count($notificacion);
        $top = 0;
        $data = ["cant"=>$c];
        foreach ($notificacion as $n) {
            $data[] = $n->toArray();
            $top++;
            if ($top == 10) {
                break;
            }

        }

        return new JsonResponse($data);

    }
    /**
     * @Route("/new", name="notificacion_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $notificacion = new Notificacion();
        $form = $this->createForm(NotificacionType::class, $notificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notificacion);
            $entityManager->flush();

            return $this->redirectToRoute('notificacion_index');
        }

        return $this->render('notificacion/new.html.twig', [
            'notificacion' => $notificacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notificacion_show", methods={"GET"})
     */
    public function show(Notificacion $notificacion): Response
    {
        return $this->render('notificacion/show.html.twig', [
            'notificacion' => $notificacion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="notificacion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Notificacion $notificacion): Response
    {
        $form = $this->createForm(NotificacionType::class, $notificacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notificacion_index');
        }

        return $this->render('notificacion/edit.html.twig', [
            'notificacion' => $notificacion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="notificacion_delete", methods={"POST"})
     */
    public function delete(Request $request, Notificacion $notificacion): Response
    {
        if ($this->isCsrfTokenValid('delete' . $notificacion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($notificacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('notificacion_index');
    }


    /**
     * @Route("/leida/{id}", name="notificacion_leida", methods={"POST"})
     */
    public function leida(Notificacion $notificacion): Response
    {
    $notificacion->setFechaLeido(new \DateTime('now'));
    $entityManager=$this->getDoctrine()->getManager();
    $entityManager->flush();
 $url= $this->generateUrl('paciente_show',['id'=>$notificacion->getPaciente()->getId()]);
   return new JsonResponse(["url"=>$url]);
    }


}
