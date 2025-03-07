<?php
                    namespace App\Form;

                    use Symfony\Component\Form\AbstractType;
                    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
                    use Symfony\Component\Form\FormBuilderInterface;
                    use Symfony\Component\OptionsResolver\OptionsResolver;
                    use Symfony\Component\Validator\Constraints\NotBlank;
                    use Symfony\Component\Validator\Constraints\Length;
                    use Symfony\Component\Validator\Constraints\Callback;
                    use Symfony\Component\Validator\Context\ExecutionContextInterface;

                    class ChangePasswordType extends AbstractType
                    {
                        public function buildForm(FormBuilderInterface $builder, array $options)
                        {
                            $builder
                                ->add('newPassword', PasswordType::class, [
                                    'label' => 'Neues Passwort',
                                    'attr' => ['class' => 'form-input mb-3'],
                                    'constraints' => [
                                        new NotBlank([
                                            'message' => 'Bitte geben Sie ein neues Passwort ein',
                                        ]),
                                        new Length([
                                            'min' => 12,
                                            'minMessage' => 'Ihr Passwort muss mindestens {{ limit }} Zeichen lang sein',
                                        ]),
                                    ],

                                ])
                                ->add('confirmNewPassword', PasswordType::class, [
                                    'label' => 'Neues Passwort bestätigen',
                                    'attr' => ['class' => 'form-input mb-3'],
                                    'mapped' => false,
                                    'constraints' => [
                                        new NotBlank([
                                            'message' => 'Bitte bestätigen Sie Ihr neues Passwort',
                                        ]),
                                    ],
                                ]);
                        }

                        public function configureOptions(OptionsResolver $resolver)
                        {
                            $resolver->setDefaults([]);
                        }
                    }