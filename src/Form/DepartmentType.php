<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('umew', null, [
                'label' => 'U.M.E.W',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('year', null, [
                'label' => 'Jahr',
                'attr' => ['class' => 'form-input mb-3']
            ])
            ->add('headOfDepartment', EntityType::class, [
                'class' => User::class,
                'label' => 'Abteilungsleiter',
                'attr' => ['class' => 'form-input mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Department::class,
        ]);
    }
}
