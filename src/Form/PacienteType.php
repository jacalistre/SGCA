<?php

namespace App\Form;

use App\Entity\Municipio;
use App\Entity\Paciente;
use App\Entity\Provincia;
use App\EventListener\AddFieldSubscriber;
use App\Repository\MunicipioRepository;
use App\Repository\ProvinciaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PacienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Nombres']])
            ->add('apellidos', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Apellidos']])
            ->add('carnet', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Carnet de Identidad']])
            ->add('pasaporte', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Pasaporte']])
            ->add('edad', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Edad']])
            ->add('sexo', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Sexo',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Masculino' => true,
                    'Femenino' => false

                ],

            ])
            ->add('color', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Color de la Piel',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Blanca' => 'B',
                    'Mestiza' => 'M',
                    'Negra' => 'N'
                ],

            ])
            ->add('direccion_ci', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Direccion CI']])
            ->add('direccion_res', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Direccion Residencia']])
            ->add('epidemiologia', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Epidemiologia',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Sospechoso' => 'Sospechoso',
                    'Confirmado' => 'Confirmado'
                ],

            ])
            ->add('sintomatologia', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Sintomatologia',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Sintomatico' => 'Sintomatico',
                    'Asintomatico' => 'Asintomatico'
                ],

            ])
            ->add('riesgo', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Riesgo',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Alto Riesgo' => 'Alto',
                     'Mediano Riesgo'=>'Mediano',
                    'Bajo Riesgo' => 'Bajo'

                ],

            ])
            ->add('transportable', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Transportable',
                'required' => true,
                'multiple' => false,
                'expanded' => false,


                'choices' => [
                    'Acostado' => 'Acostado',
                    'Sentado' => 'Sentado'
                ],

            ])
            ->add('vacuna', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Vacuna',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Abdala' => 'Abdala',
                    'Soberana' => 'Soberana'
                ],

            ])
            ->add('dosis', ChoiceType::class, ['attr' => ['class' => 'selectpicker form-control'], 'placeholder' => 'Dosis',
                'required' => false,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3'
                ],

            ])
          /*  ->add('consultorio', null, ['attr' => ['class' => 'selectpicker form-control consultorio'], 'placeholder' => 'Consultorio',
                'required' => true,
                'choices'=>[]
            ])*/
            ->add('hta', null, [])
            ->add('dm')
            ->add('epoc')
            ->add('ab')
            ->add('obeso')
            ->add('ci')
            ->add('vih')
            ->add('trastornos')
            ->add('inmunodeprimido')
            ->add('transporte_sanitario')
            ->add('cancer')
            ->add('otros')
            ->add('fc', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Frecuencia Cardiaca']])
            ->add('fr', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Frecuencia Respiratoria']])
            ->add('ta', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Tension Arterial']])
            ->add('saturacion', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'placeholder' => 'Saturacion de Oxigeno']])
            ->add('observaciones', null, ['attr' => ['title' => "No puede contener número", 'class' => 'form-control', 'style' => 'width:100%']])
           /* ->add('municipio', EntityType::class, ['class'=>Municipio::class,'attr' => ['class' => 'selectpicker form-control municipio'], 'placeholder' => 'Municipio',
                'required' => true,
                'choices'=>[]
            ])
            ->add('area', null, ['attr' => ['class' => 'selectpicker form-control area'], 'placeholder' => 'Area de Salud',
                'required' => true,
                'choices'=>[]
            ])*/
            ->add('provincia', EntityType::class, ['class'=>Provincia::class,'attr' => ['class' => 'selectpicker form-control provincia'], 'placeholder' => 'Provincia',
                'required' => true,
                ]);
        $builder->addEventSubscriber(new AddFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paciente::class,
            'validation_groups' => false,
        ]);
    }
}
