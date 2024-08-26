<?php

namespace App\Form;

use App\Entity\Notificacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mensaje')
            ->add('fecha_envio')
            ->add('fecha_leido')
            ->add('destino')
            ->add('origen')
            ->add('paciente')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Notificacion::class,
        ]);
    }
}
