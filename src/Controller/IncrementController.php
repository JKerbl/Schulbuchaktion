<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Parameter;
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
        // Get the highest year and all departments of it
        $highestYear = $dr->findHighestYear();
        $departments = $dr->findAllByYear($highestYear);

        foreach ($departments as $department){
            $newDepartment = new Department();

            // Creates the department for the new year but changes the values to 0
            $newDepartment->setName($department->getName());
            $newDepartment->setYear($highestYear + 1);
            $newDepartment->setHeadOfDepartment(null);
            $newDepartment->setBudget(0);
            $newDepartment->setUsedBudget(0);
            $newDepartment->setUmew(0);
            $em->persist($newDepartment);

            $classes = $scr->findAllByDepartmentID($department->getId());

            // Creates all the classes in the new year and sets the values to 0
            foreach ($classes as $class) {
                $newClass = new SchoolClass();

                $newClass->setName($class->getName());
                $newClass->setGrade($class->getGrade());
                $newClass->setStudentsAmount(0);
                $newClass->setRepAmount(0);
                $newClass->setYear($highestYear + 1);
                $newClass->setDepartment($newDepartment);
                $newClass->setBudget(0);
                $newClass->setUsedBudget(0);
                $em->persist($newClass);
            }
        }

        $parameterRepo = $em->getRepository(Parameter::class);

        $parameter = $parameterRepo->findOneBy(['year' => $highestYear]);

        if ($parameter){
            $newParameter = new Parameter();
            $newParameter->setYear($highestYear + 1);
            $newParameter->setLimit3100($parameter->getLimit3100());
            $newParameter->setLimit4100($parameter->getLimit4100());
            $newParameter->setLimitRK3100($parameter->getLimitRK3100());
            $newParameter->setLimitRK4100($parameter->getLimitRK4100());
            $em->persist($newParameter);
        }
        $em->flush();

        return $this->redirectToRoute('app_department_index', [], Response::HTTP_SEE_OTHER);
    }
}