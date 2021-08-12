<?php

namespace App\Form;

use App\Entity\Centro;
use App\EventListener\AddFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Nombre']])

            ->add('descripcion',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Descripcion']])

            ->add('tipo', ChoiceType::class, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Tipo de Centro',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>['Laboratorio'=>'Laboratorio','Hospital Infantil'=>'Hospital Infantil','Hospital'=>'Hospital','Centro Asistencial'=>'Centro Asistencial','Centro de Positivos'=>'Centro de Positivos','Centro de Sospechosos'=>'Centro de Sospechosos']

            ])
            ->add('provincia', null, ['attr'=>['class'=>'selectpicker form-control provincia'],'placeholder'=>'Provincia',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


            ])
            ->add('municipio', null, ['attr'=>['class'=>'selectpicker form-control municipio'],'placeholder'=>'Municipio',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>[]

            ])
        ;
        $builder->addEventSubscriber(new AddFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Centro::class,
        ]);
    }
}
