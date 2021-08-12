<?php

namespace App\Form;

use App\Entity\Usuario;
use App\EventListener\AddFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario',null,['attr'=>['title'=>"No puede contener número ni espacios ni existir otro usuario con este nombre",'class'=>'form-control','placeholder'=>'Usuario']])
            ->add('pass',RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('attr' => ['class'=>'form-control','placeholder'=>'Contrasena']),
                'second_options' => array('attr'=>['class'=>'form-control','placeholder'=>'Repetir Contrasena']),
                'invalid_message' => 'Las contraseñas no coinciden ',
            ))
            ->add('nombre',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía ",'class'=>'form-control','placeholder'=>'Nombre']])
            ->add('apellidos',null,['attr'=>['title'=>"Debe empezar con mayúsculas, no puede contener número ni ser una cadena vacía",'class'=>'form-control','placeholder'=>'Apellidos']])
            ->add('roles', ChoiceType::class, ['attr'=>['class'=>'selectpicker form-control','onchange'=>'SetRoles(this)'],'placeholder'=>'Seleccione el Rol',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices'  => [
                    'Centro' => 'ROLE_CENTRO',
                    'Administrador del Sistema' => 'ROLE_ADMIN',
                    'Administrador Municipal' => 'ROLE_ADMIN_MUN',
                    'Area de Salud'=>"ROLE_AREA",
                    "Centro Coordinador Municipal"=>"ROLE_COORDINADOR_MUNICIPAL",
                    "Centro Coordinador Provincial"=>"ROLE_COORDINADOR_PROVINCIAL",
                    "Hospital"=>"ROLE_HOSPITAL",
                    "Laboratorio"=>"ROLE_LABORATORIO",
                ],

            ])
            ->add('centro', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Centro',
                'required' => false,
                'multiple' => false,
                'expanded' => false,'choices'=>[]

            ])
            ->add('area', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Area de Salud',
                'required' => false,
                'multiple' => false,
                'expanded' => false,'choices'=>[]

            ])
            ->add('provincia', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Provincia',
                'required' => true,
                'multiple' => false,
                'expanded' => false

            ])
            ->add('municipio', null, ['attr'=>['class'=>'selectpicker form-control'],'placeholder'=>'Municipio',
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
            'data_class' => Usuario::class,
        ]);
    }
}
