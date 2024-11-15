<?php

namespace App\Form;

use App\Entity\ImportSubjectMap;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportSubjectMapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'attr' => ['class' => 'form-input mb-3', 'readonly' => true]
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'fullname',
                'label' => 'Fachbereich',
                'attr' => ['class' => 'form-input mb-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImportSubjectMap::class,
        ]);
    }
}
