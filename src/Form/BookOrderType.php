<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\BookOrder;
use App\Entity\SchoolClass;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class BookOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderFor', null, [
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Bestellen für',
            ])
            ->add('teachercopy', HiddenType::class, [
                'attr' => [
                    'class' => 'd-none form-input mb-3',
                ],
            ])
            ->add('eBook', null, [
                'attr' => [
                    'class' => 'form-input-checkbox mb-3',
                ],
                'label' => 'eBook',
            ])
            ->add('eBookPlus', null, [
                'attr' => [
                    'class' => 'form-input-checkbox mb-3',
                ],
                'label' => 'eBookPlus',
            ])
            ->add('schoolclass', EntityType::class, [
                'class' => SchoolClass::class,
                'choice_label' => 'id',
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Klasse',
            ])
            ->add('book', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'shortTitle',
                'disabled' => true,
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Buch',
            ])
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'form-input mb-3',
                ],
                'label' => 'Gegenstand',
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
