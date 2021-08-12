<?php

namespace App\Controller;

use App\Entity\Centro;
use App\Form\CentroType;
use App\Repository\CentroRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/centro")
 */
class CentroController extends AbstractController
{
    /**
     * @Route("/", name="centro_index", methods={"GET"})
     */
    public function index(CentroRepository $centroRepository): Response
    {
        $user=$this->getUser();
        $centros=[];
        if($user->getRoles()=="ROLE_COORDINADOR_MUNICIPAL" or $user->getRoles()=="ROLE_ADMIN_MUN"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),$user->getMunicipio());
        }else if($user->getRoles()=="ROLE_CENTRO" || $user->getRoles()=="ROLE_HOSPITAL"){
            $centros=[$user->getCentro()];
        }else if($user->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),null);
        }else if($user->getRoles()=="ROLE_ADMIN"){
            $centros=$centroRepository->findAll();
        }

        return $this->render('centro/index.html.twig', [
            'centros' =>$centros,
        ]);
    }

    /**
     * @Route("/new", name="centro_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $centro = new Centro();
        $form = $this->createForm(CentroType::class, $centro);
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
            $entityManager->persist($centro);
            $entityManager->flush();

            return $this->redirectToRoute('centro_index');
        }

        return $this->render('centro/new.html.twig', [
            'centro' => $centro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="centro_show", methods={"GET"})
     */
    public function show(Centro $centro): Response
    {
        return $this->render('centro/show.html.twig', [
            'centro' => $centro,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="centro_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Centro $centro): Response
    {
        $form = $this->createForm(CentroType::class, $centro);
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
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('centro_index');
        }

        return $this->render('centro/edit.html.twig', [
            'centro' => $centro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="centro_delete", methods={"POST"})
     */
    public function delete(Request $request, Centro $centro): Response
    {
        if ($this->isCsrfTokenValid('delete'.$centro->getId(), $request->request->get('_token'))) {
           try{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($centro);
            $entityManager->flush();
            $this->addFlash('success', "Centro eliminado satisfactoriamente");

        } catch (ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', "No se puede eliminar el centro porque existen pacientes asignados a el");
    }
        }

        return $this->redirectToRoute('centro_index');
    }
}
