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
            ->add('name', null, ['label' => 'Name'])
            ->add('grade', null, ['label' => 'Schulstufe'])
            ->add('studentsAmount', null, ['label' => 'Studenten Anzahl'])
            ->add('repAmount', null, ['label' => 'Repetenten Anzahl'])
            ->add('usedBudget', null, ['label' => 'Benutztes Budget'])
            ->add('budget', null, ['label' => 'Gesamt Budget'])
            ->add('year', null, ['label' => 'Jahr'])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => 'Abteilung'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolClass::class,
        ]);
    }
}
