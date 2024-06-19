<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget', name: 'app_budget.')]

class BudgetController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(SchoolClassRepository $scr, DepartmentRepository $dr): Response
    {
        $classes = $scr->findAll();
        $departments = $dr->findAll();

        $years = [];
        foreach ($classes as $class) {
            if (!in_array($class->getYear(), $years)) {
                $years[] = $class->getYear();
            }
        }

        return $this->render('budget/index.html.twig', [
            'departments' => $departments,
            'years' => $years,
        ]);
    }

    #[Route('/get-departments/{year}', name: 'get_departments')]
    public function getDepartments($year, SchoolClassRepository $scr)
    {
        $classes = $scr->findBy(['year' => $year]);

        if (!$classes) {
            throw $this->createNotFoundException('No classes found for year ' . $year);
        }

        $departments = [];
        foreach ($classes as $class) {
            $department = $class->getDepartment();
            if ($department) {
                $departments[$department->getId()] = $department->getName();
            }
        }

        if (empty($departments)) {
            throw $this->createNotFoundException('No departments found for year ' . $year);
        }

        $data = [];
        foreach ($departments as $deptId => $name) {
            $data[] = [
                'id' => $deptId,
                'name' => $name
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/get-classes/{departmentId}', name: 'get_classes')]
    public function getClasses($departmentId, SchoolClassRepository $scr, DepartmentRepository $dr)
    {
        $department = $dr->find($departmentId);

        if (!$department) {
            throw $this->createNotFoundException('No department found for ID ' . $departmentId);
        }

        $classes = $scr->findBy(['department' => $department]);

        if (!$classes) {
            throw $this->createNotFoundException('No classes found for department ' . $department->getName());
        }

        $data = [];
        foreach ($classes as $class) {
            $data[] = [
                'id' => $class->getId(),
                'name' => $class->getName()
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/get-budget/{type}/{id}', name: 'get_budget')]
    public function getBudget(string $type, int $id, SchoolClassRepository $scr, DepartmentRepository $dr)
    {
        $budget = 0;
        $usedBudget = 0;

        switch ($type) {
            case 'year':

                $classes = $scr->findBy(['year' => $id]);
                foreach ($classes as $class) {
                    $budget += $class->getDepartment()->getBudget();
                    $usedBudget += $class->getDepartment()->getUsedBudget();
                }
                break;
            case 'department':
                $department = $dr->find($id);
                $budget = $department->getBudget();
                $usedBudget = $department->getUsedBudget();
                break;
            case 'class':
                $class = $scr->find($id);
                $budget = $class->getBudget();
                $usedBudget = $class->getUsedBudget();
                break;
        }

        $data = [
            'budget' => $budget,
            'usedBudget' => $usedBudget,
        ];

        return new JsonResponse($data);
    }
}
