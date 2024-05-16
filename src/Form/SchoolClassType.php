<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\SchoolClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('grade', null, [
                'label' => 'Schulstufe',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('studentsAmount', null, [
                'label' => 'Schüler Anzahl',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('repAmount', null, [
                'label' => 'Repetenten Anzahl',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('usedBudget', null, [
                'label' => 'Benutztes Budget',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('budget', null, [
                'label' => 'Gesamt Budget',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('year', null, [
                'label' => 'Jahr',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => 'Abteilung',
                'attr' => ['class' => 'form-input mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolClass::class,
        ]);
    }
}
