<?php

namespace App\Form;

use App\Entity\Parameter;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('limit4100', null, [
                'label' => 'Limit 4100',
                'attr' => ['class' => 'form-input mb-3']
                ])
            ->add('limit3100', null, [
                'label' => 'Limit 3100',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('limitRK4100', null, [
                'label' => 'Limit RK 4100',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('limitRK3100', null, [
                'label' => 'Limit RK 3100',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('year', null, [
                'label' => 'Jahr',
                'data' => $options['year'],
                'attr' => ['readonly' => true, 'class' => 'form-input mb-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
            'year' => null,
        ]);
    }
}
