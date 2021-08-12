<?php

namespace App\Form;

use App\Entity\Consultorio;
use App\EventListener\AddFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsultorioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Nombre']])
            ->add('area', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Area de Salud',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


            ])
            ->add('provincia', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Provincia',
                'required' => true,
                'multiple' => false,
                'expanded' => false

            ])
            ->add('municipio', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Municipio',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


            ])
        ;
        $builder->addEventSubscriber(new AddFieldSubscriber());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consultorio::class,
            'validation_groups' => false,
        ]);
    }
}
