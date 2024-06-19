<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Repository\BookOrderRepository;
use App\Repository\BookRepository;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController {
    #[Route('/order/{id}', name: 'order')]
    public function order($id, BookRepository $br, SchoolClassRepository $scr): Response {
        $res = array();
        $books = $br->findAll();
        foreach ($books as $key => $book) {
            $res[] = array(
                'id' => $book->getId(),
                'shortTitle' => $book->getShortTitle()
            );
        }
        $user = $this->getUser();

        $book = $br->find($id);
        $classes = $scr->findAll();

        if ($classes === []) {
            return $this->render('home/index.html.twig', [
                'results' => $res, 'user' => $user,
                'searchInput' => ""
            ]);
        }

        return $this->render('order/index.html.twig', [
            'user' => $user, 'book' => $book,
            'classes' => $classes,
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
        return $this->render('order/overview.html.twig', [
            'departments' => $departmentRepository->findAll(),
            'orders' => $bookOrderRepository->findAll(),
        ]);
    }

    #[Route('/orderbooks/getDepartment/{departmentId<\d+>?0}', name: 'order.getDepartment')]
    public function getDepartment($departmentId, BookOrderRepository $bookOrderRepository): Response
    {
        if ($departmentId == 0) {
            $orders = $bookOrderRepository->findAll();

        } else {
            $orders = $bookOrderRepository->getOrdersOfDepartment($departmentId);
        }
        $ordersArray = [];

        foreach ($orders as $order) {
            $ordersArray[] = [
                'id' => $order->getId(),
                'count' => $order->getCount(),
                'teacherCopy' => $order->getTeacherCopy(),
                'eBook' => $order->getEBook(),
                'eBookPlus' => $order->getEBookPlus(),
                'schoolclass' => $order->getSchoolclass() ? $order->getSchoolclass()->getName() : 'Nicht zugewiesen',
                'book' => $order->getBook() ? $order->getBook()->getTitle() : 'Nicht zugewiesen',
            ];
        }

        return new JsonResponse(['orders' => $ordersArray]);
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

    #[Route('/orders', name: 'app_orders')]
    public function showOrders()
    {
        return $this->render('order/index.html.twig');
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

            $class = $scr->find($classId);
            $book = $br->find($bookId);
            $department = $departmentRepository->find($class->getDepartment()->getId());

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

            $em->persist($bookOrder);
            $em->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()], 500);
        }
    }
}
