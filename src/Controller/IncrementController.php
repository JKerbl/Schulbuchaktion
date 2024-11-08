<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\SchoolClass;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/increment')]
class IncrementController extends AbstractController
{
    #[Route('/all', name: 'app_increment')]
    public function index(EntityManagerInterface $em, DepartmentRepository $dr, SchoolClassRepository $scr): Response
    {
        $highestYear = $dr->findHighestYear();

        $departments = $dr->findAllByYear($highestYear);

        foreach ($departments as $department){
            $newDepartment = new Department();

            // "Clones" the departments and increments the year
            $newDepartment->setName($department->getName());
            $newDepartment->setYear($highestYear + 1);
            $newDepartment->setHeadOfDepartment($department->getHeadOfDepartment());
            $newDepartment->setBudget($department->getBudget());
            $newDepartment->setUsedBudget(0);
            $newDepartment->setUmew($department->getUmew());
            $em->persist($newDepartment);

            $classes = $scr->findAllByDepartmentID($department->getId());

            // Increments all classes in the department
            foreach ($classes as $class) {
                $newClass = new SchoolClass();

                $classWithoutGrade = substr($class->getName(), 1 );

                if ($class->getGrade() != 5) {
                    $className = $class->getName();

                    $newClass->setName(($class->getGrade() + 1) . $classWithoutGrade);
                    $newClass->setGrade($class->getGrade() + 1);
                    $newClass->setStudentsAmount($class->getStudentsAmount() + $class->getRepAmount());
                } else {
                    $newClass->setGrade(1);

                    $newClass->setName("1" . $classWithoutGrade);
                    $newClass->setGrade(1);
                    $newClass->setStudentsAmount(0);
                }
                $newClass->setRepAmount(0);
                $newClass->setYear($highestYear + 1);
                $newClass->setDepartment($newDepartment);
                $newClass->setBudget($class->getBudget());
                $newClass->setUsedBudget(0);
                $em->persist($newClass);
            }
        }
        $em->flush();

        return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
    }



}
