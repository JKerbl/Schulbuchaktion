<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/department')]
class DepartmentController extends AbstractController
{
    #[Route('/', name: 'app_department_index', methods: ['GET'])]
    public function index(DepartmentRepository $departmentRepository, Request $request): Response
    {
        $user = $this->getUser();
        // Get the year from the query string or set the current year
        $year = $request->query->get('year', date('Y'));
        $years = $departmentRepository->findAllYears();

        // check if the year is in the array of years
        if (!in_array($year, $years)) {
            $years[] = $year;
            sort($years);
        }

        // Gets the Classes with the year or the current year if there is no year provided
        $departments = $departmentRepository->findAllByYear($year);

        // AV can only see their own department and the admin can see all
        if (in_array('ROLE_AV', $user->getRoles())){
            foreach ($user->getDepartments() as $dep){
                $showBudgetFor[] = $dep->getName();
            }
            if (empty($showBudgetFor)) {
                $showBudgetFor[] = "all";
            }
        } else {
            $showBudgetFor[] = "all";
        }

        return $this->render('department/index.html.twig', [
            'departments' => $departments,
            'showBudgetFor' => $showBudgetFor,
            'currentYear' => $year,
            'allYears' => $years,
            'user' => $user,
        ]);
    }

    #[Route('/year/{year}', name: 'app_department_get_year', methods: ['GET'])]
    public function getDepartmentWithYear(DepartmentRepository $departmentRepository, int $year): Response
    {
        return $this->render('department/index.html.twig', [
            'departments' => $departmentRepository->findAllByYear($year),
        ]);
    }

    #[Route('/new', name: 'app_department_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $department = new Department();
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('department/new.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/duplicate', name: 'app_department_duplicate', methods: ['GET', 'POST'])]
    public function duplicate(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        $departmentCopy = clone $department;
        $form = $this->createForm(DepartmentType::class, $departmentCopy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $duplicatedDepartment = new Department();
            $duplicatedDepartment->setName($departmentCopy->getName());
            $duplicatedDepartment->setBudget($departmentCopy->getBudget());
            $duplicatedDepartment->setUsedBudget($departmentCopy->getUsedBudget());
            $duplicatedDepartment->setUmew($departmentCopy->getUmew());
            $duplicatedDepartment->setHeadOfDepartment($departmentCopy->getHeadOfDepartment());

            $entityManager->persist($duplicatedDepartment);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_department_show', methods: ['GET'])]
    public function show(Department $department): Response
    {
        return $this->render('department/show.html.twig', [
            'department' => $department,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_department_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_department_delete', methods: ['POST'])]
    public function delete(Request $request, Department $department, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$department->getId(), $request->request->get('_token'))) {
            $entityManager->remove($department);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
    }
}
