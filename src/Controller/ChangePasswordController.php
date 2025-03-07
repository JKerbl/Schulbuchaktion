<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    #[Route('/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw $this->createAccessDeniedException('No authenticated user found.');
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            throw $this->createAccessDeniedException('No authenticated user found.');
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $confirmNewPassword = $form->get('confirmNewPassword')->getData();
            if ($newPassword === $confirmNewPassword) {
                $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encodedPassword);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Log out the user
                $this->tokenStorage->setToken(null);
                $request->getSession()->invalidate();

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Die Passwörter stimmen nicht überein.');
            }

        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Das Formular wurde nicht korrekt ausgefüllt.');
        }

        return $this->render('change_password/index.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Generate a random 12 character long password
        $randomPassword = substr(bin2hex(random_bytes(12)), 0, 12);

        $userId = $request->query->get('id');

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $hashedPassword = $passwordHasher->hashPassword($user, $randomPassword);

        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->render('change_password/admin_change.html.twig', [
            'username' => $user->getUsername(),
            'password' => $randomPassword,
        ]);
    }
}