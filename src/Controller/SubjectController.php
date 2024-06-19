<?php

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subject')]
class SubjectController extends AbstractController
{
    #[Route('/', name: 'app_subject_index', methods: ['GET'])]
    public function index(SubjectRepository $subjectRepository, UserRepository $userRepository): Response
    {

        return $this->render('subject/index.html.twig', [
            'subjects' => $subjectRepository->findAll(), 'fvs' => $userRepository->findByRole('ROLE_FV')
        ]);
    }

    #[Route('/change-fv/{id}', name: 'change_fv', methods: ['POST'])]
    public function changeFv($id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, SubjectRepository $subjectRepository): Response
    {
        $subject = $subjectRepository->find($id);

        $newFV = $request->request->get('fv');

        $user = $userRepository->findOneBy(['id' => $newFV]);

        if ($subject && $newFV) {
            $subject->setHeadOfSubject($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subject_index');
    }
}
