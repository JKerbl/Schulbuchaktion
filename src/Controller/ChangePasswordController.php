<?php
namespace App\Controller;

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
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();
            $confirmNewPassword = $form->get('confirmNewPassword')->getData();

            if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
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
                    $this->addFlash('error', 'TDie neuen Passwörter stimmen nicht überein.');
                }
            } else {
                $this->addFlash('error', 'Das derzeitige Passwort ist nicht korrekt.');
            }
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Das Formular wurde nicht korrekt ausgefüllt.');
        }

        return $this->render('change_password/index.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}