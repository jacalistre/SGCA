<?php

namespace App\Controller;

use App\Entity\EstadoCama;
use App\Entity\Municipio;
use App\Entity\Provincia;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('default');
         }
        $entityManager = $this->getDoctrine()->getManager();
        $users= $entityManager-> getRepository(Usuario::class)->findAll();
        if($users==null || count($users)==0){
            $usuario= new Usuario();
            $usuario->setNombre("Admin");
            $usuario->setApellidos("Admin");
            $usuario->setPass("admin");
            $usuario->setUsuario("admin");
            $usuario->setRoles("ROLE_ADMIN");
            $provincia=new Provincia();
            $provincia->setNombre("Default");
            $municipio= new Municipio();
            $municipio->setNombre("Default");
            $municipio->setProvincia($provincia);
            $usuario->setProvincia($provincia);
            $usuario->setMunicipio($municipio);
            $entityManager->persist($usuario);
            $entityManager->flush();

            $camaEst= new EstadoCama();
            $camaEst->setEstado("Ocupada");
            $camaEst->setTipo("Bloqueo");
            $entityManager->persist($camaEst);
            $entityManager->flush();

            $est= new EstadoCama();
            $est->setEstado("Disponible");
            $est->setTipo("Funcionamiento");
            $entityManager->persist($est);
            $entityManager->flush();

        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()

    {

     }
}
