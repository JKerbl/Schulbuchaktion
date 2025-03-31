<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ParameterRepository;
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
    public function index(UserRepository $ur, ParameterRepository $pr): Response
    {
        $users = $ur->findAll();
        $thisUser = $this->getUser();

        $allowRegistrations = $pr->findOneBy(['name' => 'allowRegistrations']);

        if ($allowRegistrations == null || $allowRegistrations->getValue() == 0) {
            $allow = 0;
        } else {
            $allow = 1;
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'thisUser' => $thisUser,
            'allowRegistrations' => $allow
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

    #[Route('/change-pageLimit', name: 'change_pagelimit', methods: ['POST', 'GET'])]
    public function changePageLimit(EntityManagerInterface $entityManager, Request $request)
    {
        $id = $request->request->get('id');
        $pageLimit = $request->request->get('pageLimit');

        if ($id != null && $pageLimit != null && is_numeric($pageLimit)) {
            $user = $entityManager->getRepository(User::class)->find($id);
            $user->setPageLimit($pageLimit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user.app_my_user');
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

    #[Route('/my-user', name: 'app_my_user')]
    public function myUser(): Response
    {
        $thisUser = $this->getUser();

        return $this->render('user/my_user.html.twig', [
            'thisUser' => $thisUser
        ]);
    }
}