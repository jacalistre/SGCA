<?php

namespace App\Controller;

use App\Entity\Municipio;
use App\Form\MunicipioType;
use App\Repository\MunicipioRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/municipio")
 */
class MunicipioController extends AbstractController
{
    /**
     * @Route("/", name="municipio_index", methods={"GET"})
     */
    public function index(MunicipioRepository $municipioRepository): Response
    {
        return $this->render('municipio/index.html.twig', [
            'municipios' => $municipioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="municipio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $municipio = new Municipio();
        $form = $this->createForm(MunicipioType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($municipio);
            $entityManager->flush();

            return $this->redirectToRoute('municipio_index');
        }

        return $this->render('municipio/new.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipio_show", methods={"GET"})
     */
    public function show(Municipio $municipio): Response
    {
        return $this->render('municipio/show.html.twig', [
            'municipio' => $municipio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="municipio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Municipio $municipio): Response
    {
        $form = $this->createForm(MunicipioType::class, $municipio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('municipio_index');
        }

        return $this->render('municipio/edit.html.twig', [
            'municipio' => $municipio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="municipio_delete", methods={"POST"})
     */
    public function delete(Request $request, Municipio $municipio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$municipio->getId(), $request->request->get('_token'))) {
         try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($municipio);
            $entityManager->flush();  $this->addFlash('success', "Municipio eliminado satisfactoriamente");

        } catch (ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', "No se puede eliminar el municipio porque existen pacientes asignados a el");
    }
        }

        return $this->redirectToRoute('municipio_index');
    }
}
