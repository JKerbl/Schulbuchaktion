<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\Department;
use App\Entity\Subject;
use App\Repository\BookOrderRepository;
use App\Repository\BookRepository;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController {
    #[Route('/order/{id}', name: 'order')]
    public function order($id, BookRepository $br, SchoolClassRepository $scr, SubjectRepository $sr): Response {
        $res = array();
        $books = $br->findAll();
        foreach ($books as $key => $book) {
            $res[] = array(
                'id' => $book->getId(),
                'shortTitle' => $book->getShortTitle()
            );
        }
        $user = $this->getUser();
        $subjects = $sr->findAll();
        $userSubject = $sr->findSubjectsByHeadOfSubjectId($user->getId());

        $book = $br->find($id);
        $classes = $scr->findAlLByYear(date('Y'));

        if ($classes === []) {
            return $this->render('home/index.html.twig', [
                'results' => $res, 'user' => $user,
                'searchInput' => "",
            ]);
        }

        return $this->render('order/index.html.twig', [
            'user' => $user, 'book' => $book,
            'classes' => $classes,'allSubjects' => $subjects,
            'userSubject' => $userSubject,
        ]);
    }


    #[Route('/get-class-data/{id}', name: 'get_class_data')]
    public function getClassData($id, SchoolClassRepository $scr) {
        $class = $scr->find($id);

        if (!$class) {
            throw $this->createNotFoundException(
                'No class found for id ' . $id
            );
        }

        $data = [
            'studentAmount' => $class->getStudentsAmount(),
            'repeaterAmount' => $class->getRepAmount(),
            'usedBudgetAmount' => $class->getUsedBudget(),
            'budgetAmount' => $class->getBudget()
        ];

        return new JsonResponse($data);
    }

    #[Route('/orderbooks/index', name: 'order.index')]
    public function index(BookOrderRepository $bookOrderRepository, DepartmentRepository $departmentRepository): Response
    {
        $year = date('Y');

        $departments = $departmentRepository->findAllByYear($year);
        $orders = $bookOrderRepository->findOrdersByYear($year);

        return $this->render('order/overview.html.twig', [
            'departments' => $departments,
            'orders' => $orders,
            'currentYear' => $year
        ]);
    }

    #[Route('/orderbooks/filter/{year}/{department}/{grade}', name: 'order.filter')]
    public function filterOrders(EntityManagerInterface $em, $year, $department, $grade): Response
    {
        if ($department == 0 && $grade == 0) {
            $orders = $em->getRepository(BookOrder::class)->findOrdersByYear($year);
        } else if ($department != 0 && $grade == 0) {
            $orders = $em->getRepository(BookOrder::class)->findOrdersByYearAndDepartment($year, $department);
        } else if ($department == 0 && $grade != 0) {
            $orders = $em->getRepository(BookOrder::class)->findOrdersByYearAndGrade($year, $grade);
        }else {
            $orders = $em->getRepository(BookOrder::class)->findOrdersByYearAndDepartmentAndGrade($year, $department, $grade);
        }

        $response = [];

        foreach ($orders as $order) {
            $response[] = [
                'id' => $order->getId(),
                'schoolclass' => $order->getSchoolclass()->getName(),
                'count' => $order->getCount(),
                'teacherCopy' => $order->getTeacherCopy(),
                'ebook' => $order->getEBook(),
                'ebookPlus' => $order->getEBookPlus(),
                'book' => $order->getBook()->getShortTitle()
            ];
        }

        return new JsonResponse([
            'orders' => $response
        ]);
    }


    #[Route('/orderbooks/getDepartments/{year}', name: 'order.getDepartment')]
    public function getDepartment($year, DepartmentRepository $dr): Response
    {
        $departments = $dr->findAllByYear($year);

        $response = [];
        foreach ($departments as $department) {
            $response[] = [
                'id' => $department->getId(),
                'name' => $department->getName()
            ];
        }

        return new JsonResponse(['departments' => $response, 'year' => $year]);
    }

    #[Route('/orderbooks/delete{id}', name: 'order.delete')]
    public function delete($id, EntityManagerInterface $entityManager, BookOrderRepository $bookOrderRepository): Response
    {
        $bookOrder = $bookOrderRepository->findOneBy(['id' => $id]);

        $book = $bookOrder->getBook();

        $schoolClass = $bookOrder->getSchoolclass();

        $department = $schoolClass->getDepartment();

        $schoolClass->setUsedBudget($schoolClass->getUsedBudget()-$bookOrder->getCount()*$book->getPrice());
        $department->setUsedBudget($department->getUsedBudget()-$bookOrder->getCount()*$book->getPrice());

        $entityManager->remove($bookOrder);
        $entityManager->flush();

        return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order', name: 'submit_order', methods: ['POST'])]
    public function submitOrder(DepartmentRepository $departmentRepository, Request $request, EntityManagerInterface $em, BookRepository $br, SchoolClassRepository $scr): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $classId = $data['classId'];
            $bookId = $data['bookId'];
            $bookAmount = $data['bookAmount'];
            $teacherCopy = $data['teacherCopy'];
            $ebookPlus = $data['ebookPlus'];
            $ebook = $data['ebook'];
            $subjectId = $data['subject'];

            $class = $scr->find($classId);
            $book = $br->find($bookId);
            $department = $departmentRepository->find($class->getDepartment()->getId());
            $subject = $em->getRepository(Subject::class)->find($subjectId);

            $class->setUsedBudget($class->getUsedBudget()+$bookAmount*$book->getPrice());
            $department->setUsedBudget($department->getUsedBudget()+$bookAmount*$book->getPrice());


            if (!$class || !$book) {
                return new JsonResponse(['success' => false, 'message' => 'Klasse oder Buch nicht gefunden.'], 404);
            }

            $bookOrder = new BookOrder();
            $bookOrder->setSchoolClass($class);
            $bookOrder->setBook($book);
            $bookOrder->setCount($bookAmount);
            $bookOrder->setTeacherCopy($teacherCopy);
            $bookOrder->setEBook($ebook);
            $bookOrder->setEBookPlus($ebookPlus);

            $bookOrder->setSubject($subject);
            $subject->addBookOrder($bookOrder);

            $em->persist($bookOrder);
            $em->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()], 500);
        }
    }
}
