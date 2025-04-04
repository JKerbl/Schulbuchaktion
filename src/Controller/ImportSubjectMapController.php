<?php

namespace App\Controller;

use App\Entity\ImportSubjectMap;
use App\Entity\Subject;
use App\Form\ImportSubjectMapType;
use App\Repository\ImportSubjectMapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/subject/map')]
class ImportSubjectMapController extends AbstractController
{
    #[Route('/', name: 'app_subject_map_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subject = $request->query->get('subject', null);
        $search = $request->query->get('search', null);

        if ($subject == 0) {
            $subject = null;
        }

        $allSubjects = $entityManager->getRepository(Subject::class)->findAll();

        if ($subject == null && $search == null) {
            $allMaps = $entityManager->getRepository(ImportSubjectMap::class)->findAll();
        } else {
            $allMaps = $entityManager->getRepository(ImportSubjectMap::class)->findForSubjectOrSearch($subject, $search);
        }

        return $this->render('subject_map/index.html.twig', [
            'currentSubject' => $subject,
            'allSubjects' => $allSubjects,
            'subject_maps' => $allMaps,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_subject_map_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $importSubjectMap = new ImportSubjectMap();
        $form = $this->createForm(ImportSubjectMapType::class, $importSubjectMap);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($importSubjectMap);
            $entityManager->flush();

            return $this->redirectToRoute('app_subject_map_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject_map/new.html.twig', [
            'subject_map' => $importSubjectMap,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_map_show', methods: ['GET'])]
    public function show(ImportSubjectMap $importSubjectMap): Response
    {
        return $this->render('subject_map/show.html.twig', [
            'subject_map' => $importSubjectMap,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_subject_map_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ImportSubjectMap $importSubjectMap, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImportSubjectMapType::class, $importSubjectMap);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_subject_map_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subject_map/edit.html.twig', [
            'subject_map' => $importSubjectMap,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subject_map_delete', methods: ['POST'])]
    public function delete(Request $request, ImportSubjectMap $importSubjectMap, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$importSubjectMap->getId(), $request->request->get('_token'))) {
            $entityManager->remove($importSubjectMap);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_subject_map_index', [], Response::HTTP_SEE_OTHER);
    }
}
