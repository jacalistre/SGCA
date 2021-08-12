<?php

namespace App\Controller;

use App\Entity\Centro;
use App\Entity\Sala;
use App\Form\SalaType;
use App\Repository\SalaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sala")
 */
class SalaController extends AbstractController
{
    /**
     * @Route("/", name="sala_index", methods={"GET"})
     */
    public function index(SalaRepository $salaRepository): Response
    {
        $user=$this->getUser();
        $salas=[];
        $centroRepository=$this->getDoctrine()->getManager()->getRepository(Centro::class);
        if($user->getRoles()=="ROLE_COORDINADOR_MUNICIPAL" or $user->getRoles()=="ROLE_ADMIN_MUN"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),$user->getMunicipio());
            foreach ($centros as $c){
                $salas=array_merge($c->getSalas()->toArray(),$salas);
            }
        }else if($user->getRoles()=="ROLE_CENTRO" || $user->getRoles()=="ROLE_HOSPITAL"){
            $salas=$user->getCentro()->getSalas()->toArray();
        }else if($user->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),null);
            foreach ($centros as $c){
                $salas=array_merge($c->getSalas(),$salas);
            }
        }else if($user->getRoles()=="ROLE_ADMIN"){
            $salas=$salaRepository->findAll();
        }



        return $this->render('sala/index.html.twig', [
            'salas' => $salas,
        ]);
    }

    /**
     * @Route("/new", name="sala_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sala = new Sala();
        $form = $this->createForm(SalaType::class, $sala);
        $form->handleRequest($request);

        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("centro");
            $form ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$user->getMunicipio()->getCentros()


            ]);

        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sala);
            $entityManager->flush();

            return $this->redirectToRoute('sala_index');
        }

        return $this->render('sala/new.html.twig', [
            'sala' => $sala,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sala_show", methods={"GET"})
     */
    public function show(Sala $sala): Response
    {
        return $this->render('sala/show.html.twig', [
            'sala' => $sala,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sala_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sala $sala): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(SalaType::class, $sala);
        $form->handleRequest($request);
        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("centro");
            $form ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$user->getMunicipio()->getCentros()


            ]);

        }
        if($user->getRoles()=="ROLE_CENTRO" || $user->getRoles()=="ROLE_HOSPITAL") {
            $form->remove('centro');
            $form->add('centro', null, ['attr'=>['class'=>'selectpicker form-control','style'=>'display:none'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false

            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sala_index');
        }

        return $this->render('sala/edit.html.twig', [
            'sala' => $sala,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sala_delete", methods={"POST"})
     */
    public function delete(Request $request, Sala $sala): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sala->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sala);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sala_index');
    }
}
