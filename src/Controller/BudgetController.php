<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\Department;
use App\Entity\Parameter;
use App\Entity\SchoolClass;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function PHPUnit\Framework\equalTo;

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

    #[Route('/calc-budget', name: 'calc_budget')]
    public function calcBudget(EntityManagerInterface $manager)
    {
        // Only calculates the Budget of the Highest Year
        $d = $manager->getRepository(Department::class);
        $sc = $manager->getRepository(SchoolClass::class);

        $year = date('Y');
        $departments = $d->findAllByYear($year);

        // Gets the parameters for the budget calculation
        $parameter = $manager->getRepository(Parameter::class)->findAllByYear($year);

        foreach ($parameter as $param){
            if ($param->getName() === "Limit_4100"){
                $limit4100 = $param->getValue();
            } else if ($param->getName() === "Limit_3100"){
                $limit3100 = $param->getValue();
            } else if ($param->getName() === "Limit_RK_4100"){
                $limitRK4100 = $param->getValue();
            } else if ($param->getName() === "Limit_RK_3100"){
                $limitRK3100 = $param->getValue();
            }
        }

        // Goes through each department
        foreach ($departments as $dep){
            $classes = $sc->findAllByDepartmentID($dep->getId());

            $higherStudents = 0;
            $technicalStudents = 0;

            // Goes through each class in the department and adds the students to the respective category
            foreach ($classes as $class){
                if ($class->getType() === "h"){
                    $class->setBudget($class->getStudentsAmount() * $limit4100);
                    $higherStudents += $class->getStudentsAmount();
                } else {
                    $class->setBudget($class->getStudentsAmount() * $limit3100);
                    $technicalStudents += $class->getStudentsAmount();
                }
            }

            // Calculates the budget for the department
            $budget = $higherStudents * $limit4100 + $technicalStudents * $limit3100;
            $dep->setBudget($budget);

            $manager->persist($dep);
        }

        $manager->flush();

        $this->calcUsedBudget($manager);
    }

    #[Route('/calc-usedBudget', name: 'calc_used_budget')]
    public function calcUsedBudget(EntityManagerInterface $em)
    {
        $d = $em->getRepository(Department::class);
        $sc = $em->getRepository(SchoolClass::class);
        $or = $em->getRepository(BookOrder::class);

        $year = date('Y');
        $departments = $d->findAllByYear($year);
        $orders = $or->findOrdersByYear($year);
        $classes = $sc->findAllByYear($year);

        foreach ($classes as $class){
            $class->setUsedBudget(0);
            $em->persist($class);
        }

        $em->flush();

        foreach ($orders as $order) {
            $class = $order->getSchoolclass();
            $book = $order->getBook();

            if ($order->getOrderFor() == 'Mit Repetenten') {
                if ($order->getTeacherCopy()) {
                    $budget = $book->getPrice() * ($class->getStudentsAmount() + $class->getRepAmount() + 1);
                } else {
                    $budget = $book->getPrice() * ($class->getStudentsAmount() + $class->getRepAmount());
                }
            } elseif ($order->getOrderFor() == 'Ohne Repetenten') {
                if ($order->getTeacherCopy()) {
                    $budget = $book->getPrice() * ($class->getStudentsAmount() + 1);
                } else {
                    $budget = $book->getPrice() * ($class->getStudentsAmount());
                }
            } elseif ($order->getOrderFor() == 'Nur Repetenten') {
                if ($order->getTeacherCopy()) {
                    $budget = $book->getPrice() * ($class->getRepAmount() + 1);
                } else {
                    $budget = $book->getPrice() * ($class->getRepAmount());
                }
            }

            $class->setUsedBudget($budget + $class->getUsedBudget());

            $em->persist($class);
        }

        $em->flush();

        foreach ($departments as $dep){
            $classes = $sc->findAllByDepartmentID($dep->getId());

            $usedBudget = 0;

            foreach ($classes as $class){
                $usedBudget += $class->getUsedBudget();
            }

            $dep->setUsedBudget($usedBudget);

            $em->persist($dep);
        }

        $em->flush();
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
