<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $regform = $this->createFormBuilder()
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'AV' => 'ROLE_AV',
                    'FV' => 'ROLE_FV',
                    'Admin' => 'ROLE_ADMIN',
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

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('registration/index.html.twig', [
            'regform' => $regform->createView(),
        ]);
    }
}