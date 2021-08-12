<?php
/**
 * Created by PhpStorm.
 * User: jomady
 * Date: 26/07/21
 * Time: 18:28
 */

namespace App\EventListener;

use App\Entity\EstadoCama;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityRepository;

class AddFieldSubscriber implements EventSubscriberInterface
{ protected $required;
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',

            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * Cuando el usuario llene los datos del formulario y haga el envío del mismo,
     * este método será ejecutado.
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form=$event->getForm();

        if (isset($data['provincia']) && $form->has('municipio')) {
            $this->addFieldMunicipio($form, $data['provincia']);
        }
        if (isset($data['municipio']) && $form->has('area')) {
            $this->addFieldArea($form, $data['municipio']);

        }
        if (isset($data['area']) && $form->has('consultorio')) {
            $this->addFieldConsultorio($form, $data['area']);
        }
        if (isset($data['municipio']) &&  $form->has('centro')) {

            $this->addFieldCentro($form, $data['municipio']);
        }
        if (isset($data['centro']) &&  $form->has('sala')) {
            $this->addFieldSala($form, $data['centro']);
        }
        if (isset($data['sala']) &&  $form->has('cama')) {
            $this->addFieldCama($form, $data['sala']);
        }


    }

    public function preSetData(FormEvent $event)
    {
        $form=$event->getForm();
        $entity = $event->getData(); //data es un objeto AppBundle\Entity\User
        $this->required=!($entity instanceof Usuario);
        if ($entity) {
            if (method_exists($entity, 'getProvincia') && method_exists($entity, "getMunicipio")) {
                $this->addFieldMunicipio($event->getForm(), $entity->getProvincia());
            }
            if (method_exists($entity, 'getMunicipio') && method_exists($entity, "getArea")) {
               $this->addFieldArea($event->getForm(), $entity->getMunicipio());
            }
            if (method_exists($entity, 'getArea') && method_exists($entity, "getConsultorio")) {
                $this->addFieldConsultorio($event->getForm(), $entity->getArea());
            }
            if ($form->has("municipio") && $form->has("centro")) {
                $this->addFieldCentro($event->getForm(), $entity->getMunicipio());
            }

            if (method_exists($entity, 'getCentro') && method_exists($entity, "getSala")) {
                $this->addFieldSala($event->getForm(), $entity->getCentro());
            }

            if (method_exists($entity, 'getSala') && method_exists($entity, "getCama")) {
                $this->addFieldCama($form, $entity->getSala());
            }
        }
    }

    protected function addFieldMunicipio(Form $form, $provincia)
    {

        $form->add('municipio', EntityType::class, array(
            'class' => 'App\Entity\Municipio',
            'query_builder' => function (EntityRepository $er) use ($provincia) {
                return $er->createQueryBuilder('p')
                    ->where('p.provincia = :provincia')
                    ->setParameter('provincia', $provincia);
            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Municipio',
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));
    }

    protected function addFieldArea(Form $form, $municipio)
    {

        $form->add('area', EntityType::class, array(
            'class' => 'App\Entity\AreaSalud',
            'query_builder' => function (EntityRepository $er) use ($municipio) {
                return $er->createQueryBuilder('a')
                    ->where('a.municipio = :municipio')
                    ->setParameter('municipio', $municipio);
            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Area de Salud',
            'required' => $this->required,
            'multiple' => false,
            'expanded' => false,
        ));
    }

    protected function addFieldConsultorio(Form $form, $area)
    {

        $form->add('consultorio', EntityType::class, array(
            'class' => 'App\Entity\Consultorio',
            'query_builder' => function (EntityRepository $er) use ($area) {
                return $er->createQueryBuilder('c')
                    ->where('c.area = :area')
                    ->setParameter('area', $area);
            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Consultorio',
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));
    }

    protected function addFieldCentro(Form $form, $municipio)
    {

        $form->add('centro', EntityType::class, array(
            'class' => 'App\Entity\Centro',
            'query_builder' => function (EntityRepository $er) use ($municipio) {
                return $er->createQueryBuilder('e')
                    ->where('e.municipio = :municipio')
                    ->setParameter('municipio', $municipio);
            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Centro',
            'required' => $this->required,
            'multiple' => false,
            'expanded' => false,
        ));
    }
    protected function addFieldSala(Form $form, $centro)
    {

        $form->add('sala', EntityType::class, array(
            'class' => 'App\Entity\Sala',
            'query_builder' => function (EntityRepository $er) use ($centro) {
                return $er->createQueryBuilder('s')
                    ->where('s.centro = :centro')
                    ->setParameter('centro', $centro);
            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Sala',
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));
    }
    protected function addFieldCama(Form $form, $sala)
    {

        $form->add('cama', EntityType::class, array(
            'class' => 'App\Entity\Cama',
            'query_builder' => function (EntityRepository $er) use ($sala) {
                return $er->createQueryBuilder('a')

                    ->innerJoin(EstadoCama::class,'e','WITH','a.estado=e')
                    ->where('a.sala = :sala')
                    ->andWhere('e.tipo not like :tipo')
                    ->setParameter('sala', $sala)
                    ->setParameter("tipo","%Bloqueo%");

            }, 'attr' => ['class' => 'selectpicker form-control '], 'placeholder' => 'Cama',
            'required' => true,
            'multiple' => false,
            'expanded' => false,
        ));
    }
}
