<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Repository\BookOrderRepository;
use App\Repository\BookRepository;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

        /*if ($classes === []) {
            return $this->render('home/index.html.twig', [
                'results' => $res, 'user' => $user,
                'searchInput' => ""
            ]);
        }*/

        return $this->render('order/index.html.twig', [
            'user' => $user, 'book' => $book,
            'classes' => $classes,
        ]);
    }

    #[Route('/orderbooks/index', name: 'order.index')]
    public function index(BookOrderRepository $bookOrderRepository, DepartmentRepository $departmentRepository): Response
    {
        return $this->render('order/overview.html.twig', [
            'departments' => $departmentRepository->findAll(),
            'orders' => $bookOrderRepository->findAll(),
        ]);
    }

    #[Route('/orderbooks/getDepartment/{departmentId}', name: 'order.getDepartment')]
    public function getDepartment($departmentId, BookOrderRepository $bookOrderRepository, DepartmentRepository $departmentRepository): Response
    {
        $orders = $bookOrderRepository->getOrdersOfDepartment($departmentId);
        $budget = $departmentRepository->find($departmentId)->getBudget();
        $usedBudget = 0;
        $ordersArray = [];

        foreach ($orders as $order) {
            $usedBudget += $order->getBook()->getPrice() * $order->getCount();

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

        return new JsonResponse(['orders' => $ordersArray, 'budget' => $budget, 'usedBudget' => $usedBudget]);
    }

    #[Route('/orders', name: 'app_orders')]
    public function showOrders()
    {
        return $this->render('order/index.html.twig');

    }

}
