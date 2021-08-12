<?php

namespace App\Controller;

use App\Entity\AreaSalud;
use App\Form\AreaSaludType;
use App\Repository\AreaSaludRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/area/salud")
 */
class AreaSaludController extends AbstractController
{
    /**
     * @Route("/", name="area_salud_index", methods={"GET"})
     */
    public function index(AreaSaludRepository $areaSaludRepository): Response
    {
        $user=$this->getUser();
        $areas=null;
        if($user->getRoles()=="ROLE_ADMIN_MUN"){
            $areas=$areaSaludRepository->getAreas($user->getProvincia(),$user->getMunicipio());

        }else{
            $areas=$areaSaludRepository->findAll();

        }
        return $this->render('area_salud/index.html.twig', [
            'area_saluds' => $areas ,
        ]);
    }

    /**
     * @Route("/new", name="area_salud_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $areaSalud = new AreaSalud();
        $form = $this->createForm(AreaSaludType::class, $areaSalud);

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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($areaSalud);
            $entityManager->flush();

            return $this->redirectToRoute('area_salud_index');
        }

        return $this->render('area_salud/new.html.twig', [
            'area_salud' => $areaSalud,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="area_salud_show", methods={"GET"})
     */
    public function show(AreaSalud $areaSalud): Response
    {
        return $this->render('area_salud/show.html.twig', [
            'area_salud' => $areaSalud,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="area_salud_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AreaSalud $areaSalud): Response
    {
        $form = $this->createForm(AreaSaludType::class, $areaSalud);
        $form->handleRequest($request);

        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
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
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('area_salud_index');
        }

        return $this->render('area_salud/edit.html.twig', [
            'area_salud' => $areaSalud,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="area_salud_delete", methods={"POST"})
     */
    public function delete(Request $request, AreaSalud $areaSalud): Response
    {
        if ($this->isCsrfTokenValid('delete'.$areaSalud->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
            $entityManager->remove($areaSalud);
            $entityManager->flush();
                $this->addFlash('success', "Area eliminada satisfactoriamente");

            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', "No se puede eliminar el area porque existen pacientes asignados a ella");
            }
        }

        return $this->redirectToRoute('area_salud_index');
    }
}
