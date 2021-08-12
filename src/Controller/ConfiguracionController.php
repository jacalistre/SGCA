<?php

namespace App\Controller;

use App\Entity\Configuracion;
use App\Form\ConfiguracionType;
use App\Repository\ConfiguracionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/configuracion")
 */
class ConfiguracionController extends AbstractController
{
    /**
     * @Route("/", name="configuracion_index", methods={"GET"})
     */
    public function index(ConfiguracionRepository $configuracionRepository): Response
    {
        return $this->render('configuracion/index.html.twig', [
            'configuracions' => $configuracionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="configuracion_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $configuracion = new Configuracion();
        $form = $this->createForm(ConfiguracionType::class, $configuracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($configuracion);
            $entityManager->flush();

            return $this->redirectToRoute('configuracion_index');
        }

        return $this->render('configuracion/new.html.twig', [
            'configuracion' => $configuracion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="configuracion_show", methods={"GET"})
     */
    public function show(Configuracion $configuracion): Response
    {
        return $this->render('configuracion/show.html.twig', [
            'configuracion' => $configuracion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="configuracion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Configuracion $configuracion): Response
    {
        $form = $this->createForm(ConfiguracionType::class, $configuracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('configuracion_index');
        }

        return $this->render('configuracion/edit.html.twig', [
            'configuracion' => $configuracion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="configuracion_delete", methods={"POST"})
     */
    public function delete(Request $request, Configuracion $configuracion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$configuracion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($configuracion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('configuracion_index');
    }
}
