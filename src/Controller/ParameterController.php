<?php

namespace App\Controller;

use App\Entity\Parameter;
use App\Form\ParameterType;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/parameter')]
class ParameterController extends AbstractController
{
    #[Route('/', name: 'app_parameter_index', methods: ['GET'])]
    public function index(ParameterRepository $parameterRepository): Response
    {
        return $this->render('parameter/index.html.twig', [
            'parameters' => $parameterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_parameter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parameter = new Parameter();
        $pr = $entityManager->getRepository(Parameter::class);

        $year = $pr->findHighestYear();

        // Goes back to the List of Parameters if there is no year
        if (!$year) {
            return $this->redirectToRoute('app_parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ParameterType::class, $parameter, ['year' => (string) $pr->findHighestYear()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($parameter);
            $entityManager->flush();

            return $this->redirectToRoute('app_parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parameter/new.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parameter_show', methods: ['GET'])]
    public function show(Parameter $parameter): Response
    {
        return $this->render('parameter/show.html.twig', [
            'parameter' => $parameter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parameter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parameter $parameter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parameter/edit.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parameter_delete', methods: ['POST'])]
    public function delete(Request $request, Parameter $parameter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parameter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($parameter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parameter_index', [], Response::HTTP_SEE_OTHER);
    }
}
