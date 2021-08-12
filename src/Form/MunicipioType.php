<?php

namespace App\Form;

use App\Entity\Municipio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MunicipioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Nombre']])

            ->add('provincia',null, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Provincia',
                    'required' => true
])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Municipio::class,
        ]);
    }
}
