<?php

namespace App\Controller;

use App\Entity\EstadoCama;
use App\Form\EstadoCamaType;
use App\Repository\EstadoCamaRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/estado/cama")
 */
class EstadoCamaController extends AbstractController
{
    /**
     * @Route("/", name="estado_cama_index", methods={"GET"})
     */
    public function index(EstadoCamaRepository $estadoCamaRepository): Response
    {
        return $this->render('estado_cama/index.html.twig', [
            'estado_camas' => $estadoCamaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="estado_cama_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $estadoCama = new EstadoCama();
        $form = $this->createForm(EstadoCamaType::class, $estadoCama);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($estadoCama);
            $entityManager->flush();

            return $this->redirectToRoute('estado_cama_index');
        }

        return $this->render('estado_cama/new.html.twig', [
            'estado_cama' => $estadoCama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estado_cama_show", methods={"GET"})
     */
    public function show(EstadoCama $estadoCama): Response
    {
        return $this->render('estado_cama/show.html.twig', [
            'estado_cama' => $estadoCama,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="estado_cama_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EstadoCama $estadoCama): Response
    {
        $form = $this->createForm(EstadoCamaType::class, $estadoCama);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estado_cama_index');
        }

        return $this->render('estado_cama/edit.html.twig', [
            'estado_cama' => $estadoCama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="estado_cama_delete", methods={"POST"})
     */
    public function delete(Request $request, EstadoCama $estadoCama): Response
    {
        if ($this->isCsrfTokenValid('delete'.$estadoCama->getId(), $request->request->get('_token'))) {
          try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($estadoCama);
            $entityManager->flush();
            $this->addFlash('success', "Estado eliminado satisfactoriamente");

        } catch (ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', "No se puede eliminar el estado porque existen camas asignados a el");
    }
        }

        return $this->redirectToRoute('estado_cama_index');
    }
}
