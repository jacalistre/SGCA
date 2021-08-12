<?php

namespace App\Form;

use App\Entity\Cama;
use App\EventListener\AddFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CamaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Descripcion']])

            ->add('numero',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Numero']])

            ->add('estado', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Estado',
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('sala', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Sala',
                'required' => true,
                'multiple' => false,
                'expanded' => false

            ])
            ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false

            ])
        ;

        $builder->addEventSubscriber(new AddFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cama::class,
        ]);
    }
}
