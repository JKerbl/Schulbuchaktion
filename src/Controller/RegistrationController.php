<?php

namespace App\Controller;

use App\Entity\Parameter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $param = $entityManager->getRepository(Parameter::class)->findOneBy(['name' => 'allowRegistrations']);

        if ($param == null || $param->getValue() == 0) {
            return $this->render('landing/registrationDisabled.html.twig');
        }

        $regform = $this->createFormBuilder()
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Ihr Passwort muss mindestens {{ limit }} Zeichen lang sein',
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                ],
                'label' => 'Role',
                'multiple' => false,
                'expanded' => false,
            ])
            ->getForm();

        $regform->handleRequest($request);

        if ($regform->isSubmitted() && $regform->isValid()) {
            $input = $regform->getData();
            $user = new User;
            $user->setUsername($input['username']);
            $user->setPassword($passwordHasher->hashPassword($user, $input['password']));
            $user->setRoles([$input['roles']]); // Assign the selected role to the user

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('app_login'));
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Der Benutzername ist bereits vergeben.');
            }
        }

        return $this->render('registration/index.html.twig', [
            'regform' => $regform->createView(),
        ]);
    }

    #[Route('/toggleRegistration', name: 'toggle_registration')]
    public function toggleRegistration(EntityManagerInterface $em)
    {
        $pr = $em->getRepository(Parameter::class);

        $param = $pr->findOneBy(['name' => 'allowRegistrations']);

        if ($param == null) {
            $param = new Parameter();
            $param->setName('allowRegistrations');
            $param->setValue(1);
            $em->persist($param);
        }

        if ($param->getValue() == 1) {
            $param->setValue(0);
        } else {
            $param->setValue(1);
        }

        $em->flush();

        $status = $param->getValue() == 1 ? "Registrierungen aktiviert" : "Registrierungen deaktiviert";

        return new JsonResponse(['status' => $status]);
    }
}