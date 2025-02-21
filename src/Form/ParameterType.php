<?php

namespace App\Form;

use App\Entity\Parameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('value', null, [
                'label' => 'Wert',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('year', null, [
                'label' => 'Jahr',
                'attr' => ['class' => 'form-input mb-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parameter::class,
        ]);
    }
}
