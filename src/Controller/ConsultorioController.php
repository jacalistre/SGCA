<?php

namespace App\Controller;

use App\Entity\AreaSalud;
use App\Entity\Consultorio;
use App\Form\ConsultorioType;
use App\Repository\ConsultorioRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/consultorio")
 */
class ConsultorioController extends AbstractController
{
    /**
     * @Route("/", name="consultorio_index", methods={"GET"})
     */
    public function index(ConsultorioRepository $consultorioRepository): Response
    { if($this->getUser()->getRoles()=="ROLE_ADMIN_MUN") {
        $consultorio = $this->getUser()->getMunicipio()->getConsultorios();
    }else{
        $consultorio=$consultorioRepository->findAll();
    }
        return $this->render('consultorio/index.html.twig', [
            'consultorios' =>$consultorio ,
        ]);
    }

    /**
     * @Route("/new", name="consultorio_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $consultorio = new Consultorio();
        $form = $this->createForm(ConsultorioType::class, $consultorio);
        $form->handleRequest($request);

        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("provincia")->remove("municipio");
            $form ->add('provincia', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Provincia',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>[$user->getProvincia()]


            ])
                ->add('municipio', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Municipio',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'=>[$user->getMunicipio()]
                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $area=$entityManager->getRepository(AreaSalud::class)->find($request->request->get('consultorio')['area']);
            $consultorio->setArea($area);
            $entityManager->persist($consultorio);
            $entityManager->flush();

            return $this->redirectToRoute('consultorio_index');
        }

        return $this->render('consultorio/new.html.twig', [
            'consultorio' => $consultorio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="consultorio_show", methods={"GET"})
     */
    public function show(Consultorio $consultorio): Response
    {

        return $this->render('consultorio/show.html.twig', [
            'consultorio' => $consultorio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="consultorio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Consultorio $consultorio): Response
    {
        $form = $this->createForm(ConsultorioType::class, $consultorio);
        $form->handleRequest($request);
        $user=$this->getUser();

        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("provincia")->remove("municipio");

            $form ->add('provincia', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Provincia',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>[$user->getProvincia()]


            ])
                ->add('municipio', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Municipio',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'=>[$user->getMunicipio()]

                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager= $this->getDoctrine()->getManager();
            $area=$entityManager->getRepository(AreaSalud::class)->find($request->request->get('consultorio')['area']);
            $consultorio->setArea($area);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('consultorio_index');
        }

        return $this->render('consultorio/edit.html.twig', [
            'consultorio' => $consultorio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="consultorio_delete", methods={"POST"})
     */
    public function delete(Request $request, Consultorio $consultorio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consultorio->getId(), $request->request->get('_token'))) {
            try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($consultorio);
            $entityManager->flush();
            $this->addFlash('success', "Consultorio eliminado satisfactoriamente");

        } catch (ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', "No se puede eliminar el consultorio porque existen pacientes asignados a el");
    }
        }

        return $this->redirectToRoute('consultorio_index');
    }
}
