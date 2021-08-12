<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/usuario")
 */
class UsuarioController extends AbstractController
{
    /**
     * @Route("/", name="usuario_index", methods={"GET"})
     */
    public function index(UsuarioRepository $usuarioRepository): Response
    {$user=$this->getUser();
        $usuarios=null;
        if( $user->getRoles()=="ROLE_ADMIN_MUN"){
            $usuarios=$this->getDoctrine()->getRepository(Usuario::class)->ObtenerUsers($user->getProvincia(),$user->getMunicipio());
        }else{
            $usuarios=$usuarioRepository->findAll();
        }

        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);
/*var_dump($form->isSubmitted() && $form->isValid());
die;*/
        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("provincia")->remove("municipio")->remove("roles");

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

                ]) ->add('roles', ChoiceType::class, ['attr'=>['class'=>'selectpicker form-control','onchange'=>'SetRoles(this)'],'placeholder'=>'Seleccione el Rol',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,


                    'choices'  => [
                        'Centro' => 'ROLE_CENTRO',
                        'Area de Salud'=>"ROLE_AREA",
                        "Centro Coordinador Municipal"=>"ROLE_COORDINADOR_MUNICIPAL",
                        "Hospital"=>"ROLE_HOSPITAL",

                    ],

                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if(($usuario->getRoles()=="ROLE_AREA" && $usuario->getArea()==null) ||($usuario->getRoles()=="ROLE_CENTRO" && $usuario->getCentro()==null)  ){

                $this->addFlash("error","Debe seleccion la correspondiente area o centro para el rol que selecciono");
                return $this->render('usuario/new.html.twig', [
                    'usuario' => $usuario,
                    'form' => $form->createView(),
                ]);

            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_show", methods={"GET"})
     */
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Usuario $usuario): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        $user=$this->getUser();
        if($user->getRoles()=="ROLE_ADMIN_MUN" &&!$form->isSubmitted()){
            $form->remove("provincia")->remove("municipio")->remove("roles");

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

                ])  ->add('roles', ChoiceType::class, ['attr'=>['class'=>'selectpicker form-control','onchange'=>'SetRoles(this)'],'placeholder'=>'Seleccione el Rol',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,


                    'choices'  => [
                        'Centro' => 'ROLE_CENTRO',
                        'Area de Salud'=>"ROLE_AREA",
                        "Centro Coordinador Municipal"=>"ROLE_COORDINADOR_MUNICIPAL",
                        "Hospital"=>"ROLE_HOSPITAL",

                    ],

                ]);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/edit.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_delete", methods={"POST"})
     */
    public function delete(Request $request, Usuario $usuario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($usuario);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }
}
