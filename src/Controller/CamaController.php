<?php

namespace App\Controller;

use App\Entity\Cama;
use App\Entity\Centro;
use App\Entity\EstadoCama;
use App\Form\CamaType;
use App\Repository\CamaRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cama")
 */
class CamaController extends AbstractController
{
    /**
     * @Route("/", name="cama_index", methods={"GET"})
     */
    public function index(CamaRepository $camaRepository): Response
    {
        $user=$this->getUser();
        $camas=[];
        $centroRepository=$this->getDoctrine()->getManager()->getRepository(Centro::class);
        if($user->getRoles()=="ROLE_COORDINADOR_MUNICIPAL" or $user->getRoles()=="ROLE_ADMIN_MUN"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),$user->getMunicipio());
            foreach ($centros as $c){
                $camas=array_merge($c->getCamas()->toArray(),$camas);
            }
        }else if($user->getRoles()=="ROLE_CENTRO"){
            $camas=$user->getCentro()->getCamas()->toArray();
        }
        else if( $user->getRoles()=="ROLE_HOSPITAL"){
            $camas=$user->getCentro()->getCamas()->toArray();
            foreach ($user->getCentro()->getCentros() as $c){
                $camas=array_merge($camas,$c->getCamas()->toArray());
            }
        }
        else if($user->getRoles()=="ROLE_COORDINADOR_PROVINCIAL"){
            $centros=$centroRepository->findCentrosRol($user->getProvincia(),null);
            foreach ($centros as $c){
                $camas=array_merge($c->getCamas()->toArray(),$camas);
            }
        }else if($user->getRoles()=="ROLE_ADMIN"){
            $camas=$camaRepository->findAll();
        }

        return $this->render('cama/index.html.twig', [
            'camas' => $camas,
        ]);
    }

    /**
     * @Route("/new", name="cama_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cama = new Cama();
        $form = $this->createForm(CamaType::class, $cama);
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
        if(($user->getRoles()=="ROLE_HOSPITAL" or $user->getRoles()=="ROLE_CENTRO" ) &&!$form->isSubmitted()){
            $form->remove("centro");
            $centros=[$user->getCentro()];
            $centros=array_merge($centros,$user->getCentro()->getCentros()->toArray());
            $form ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$centros


            ]);

        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cama);
            $entityManager->flush();

            return $this->redirectToRoute('cama_index');
        }

        return $this->render('cama/new.html.twig', [
            'cama' => $cama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/multiple", name="cama_multiple_new", methods={"GET","POST"})
     */
    public function newmultiple(Request $request): Response
    {
        $cama = new Cama();
        $form = $this->createForm(CamaType::class, $cama);
        $form->remove("numero");
        $form->add("numero",null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Cantidad']]);
         if( $this->getUser()->getRoles()=="ROLE_CENTRO"){
             $form->remove("centro");
             $form->add("centro",null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Cantidad']
             ,'choices'=>[$this->getUser()->getCentro()]]);
             $form->remove("sala");
             $form->add("sala",null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Cantidad']
                 ,'choices'=>$this->getUser()->getCentro()->getSalas()->toArray()]);
         }
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
        if($user->getRoles()=="ROLE_HOSPITAL" &&!$form->isSubmitted()){
            $form->remove("centro");
            $centros=[$user->getCentro()];
            $centros=array_merge($centros,$user->getCentro()->getCentros()->toArray());
            $form ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>$centros


            ]);

        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $last_cama=$entityManager->getRepository(Cama::class)->EncontrarUltimaCama($cama->getCentro()->getId(),$cama->getSala()->getId());
            $last=($last_cama==null?0:$last_cama->getNumero());

            for($i=$last+1;$i<=$last+$cama->getNumero();$i++){
                $nb=null;
                $nb= new Cama();
                $nb->setCentro($cama->getCentro());
                $nb->setNumero($i);
                $nb->setEstado($cama->getEstado());
                $nb->setDescripcion($cama->getDescripcion());
                $nb->setSala($cama->getSala());
                $entityManager->persist($nb);
                $entityManager->flush();
            }

            return $this->redirectToRoute('cama_index');
        }

        return $this->render('cama/new.html.twig', [
            'cama' => $cama,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="cama_show", methods={"GET"})
     */
    public function show(Cama $cama): Response
    {
        return $this->render('cama/show.html.twig', [
            'cama' => $cama,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cama_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cama $cama): Response
    {
        if($cama->getEstado()->getEstado()=="Ocupada"){
            $this->addFlash("error","No se puede editar una cama mientras esta ocupada");
            return $this->redirectToRoute('cama_index');
        }
        $user=$this->getUser();
        $form = $this->createForm(CamaType::class, $cama);
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
        if(($user->getRoles()=="ROLE_CENTRO" || $user->getRoles()=="ROLE_HOSPITAL") && !$form->isSubmitted()) {

            $estados=$this->getDoctrine()->getRepository(EstadoCama::class)->ObtenerEstados();
            $form->remove("centro")->remove("sala")
                ->add('sala', null, ['attr'=>['class'=>'selectpicker form-control','style'=>'display:none'],'placeholder'=>'Sala',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false

                ])
                ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control','style'=>'display:none'],'placeholder'=>'Centro',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false
                ]) ->add('estado', EntityType::class, ['class'=>EstadoCama::class,'choice_name'=>'estado','attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Estado',
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'=>$estados
                ])
            ;

        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cama_index');
        }

        return $this->render('cama/edit.html.twig', [
            'cama' => $cama,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cama_delete", methods={"POST"})
     */
    public function delete(Request $request, Cama $cama): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cama->getId(), $request->request->get('_token'))) {
           try{
               if($cama->getEstado()!="Ocupada") {
                   $entityManager = $this->getDoctrine()->getManager();
                   $entityManager->remove($cama);
                   $entityManager->flush();

                   $this->addFlash('success', "Cama eliminada satisfactoriamente");
               }else{
                   $this->addFlash('error', "No se puede eliminar una cama que se encuentra ocupada");

               }
        } catch (ForeignKeyConstraintViolationException $e) {
        $this->addFlash('error', "No se puede eliminar la cama porque existen pacientes asignados a ella");
    }
        }

        return $this->redirectToRoute('cama_index');
    }
    /**
     * @Route("/deletemultiple/{data}", name="cama_delete_multiple", methods={"GET"})
     */
    public function deletemultiple($data): Response
    {
        $entityManager = $this->getDoctrine()->getManager();


                $values=explode('-',$data);
                $errors=0;
                $message="";
                foreach($values as $v){
                    if($v!=''){
                        try{
                        $cama=$entityManager->getRepository(Cama::class)->find($v);

                            if($cama->getEstado()!="Ocupada") {
                                $entityManager->remove($cama);
                                $entityManager->flush();

                            }else{
                                $message.="No se puede eliminar la cama ".$cama->getNumero()." porque se encuentra ocupada,";
                                $errors++;
                            }
                        } catch (ForeignKeyConstraintViolationException $e) {
                           $message.="No se puede eliminar la cama ".$cama->getNumero()." porque existen pacientes asignados a ella,";
                            $errors++;

                        }
                    }
                }
                if($errors==0){
                    $this->addFlash("success","Todas las camas han sido eliminadas satisfactoriamente");
                }else{
                    $this->addFlash('error', $message);

                }
        return $this->redirectToRoute('cama_index');
    }

}
