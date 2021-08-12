<?php

namespace App\Form;

use App\Entity\EstadoCama;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EstadoCamaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estado',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Estado']])

            ->add('descripcion',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Descripcion']])
            ->add('tipo', ChoiceType::class, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Tipo',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=>['Funcionamiento'=>'Funcionamiento','Bloqueo'=>'Bloqueo']

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EstadoCama::class,
        ]);
    }
}
