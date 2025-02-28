<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user.')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $ur): Response
    {
        $users = $ur->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/change-role/{id}', name: 'change_role', methods: ['POST'])]
    public function changeRole($id, Request $request, EntityManagerInterface $entityManager, UserRepository $ur): Response
    {
        $user = $ur->find($id);
        $newRole = $request->request->get('role');

        if ($user && $newRole) {
            $user->setRoles([$newRole]);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user.index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id, EntityManagerInterface $entityManager, UserRepository $ur): Response
    {
        $user = $ur->find($id);

        if ($user->getDepartments() != null) {
            foreach ($user->getDepartments() as $department) {
                $department->setHeadOfDepartment(null);
            }
        }

        if ($user->getSubject() != null) {
            foreach ($user->getSubject() as $subject) {
                $subject->setHeadOfSubject(null);
            }
        }

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user.index');
    }
}