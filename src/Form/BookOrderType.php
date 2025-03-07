<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookOrder;
use App\Entity\SchoolClass;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class BookOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'shortTitle',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Buch',
            ])
            ->add('schoolclass', EntityType::class, [
                'class' => SchoolClass::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Klasse',
            ])
            ->add('orderFor', ChoiceType::class, [
                'choices' => [
                    'Mit Repetenten' => 'Mit Repetenten',
                    'Nur Repetenten' => 'Nur Repetenten',
                    'Ohne Repetenten' => 'Ohne Repetenten',
                ],
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Bestellen für',
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Gegenstand',
            ])
            ->add('eBook', null, [
                'attr' => [
                    'class' => 'form-input-checkbox mb-3',
                ],
                'label' => 'eBook',
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookOrder::class,
        ]);
    }
}
