<?php

namespace App\Controller;

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
        $orders = $bookOrderRepository->findAll();
        $user = $this->getUser();
        $departments = $departmentRepository->findAll();

        return $this->render('order/overview.html.twig', [
            'orders' => $orders,'user' => $user, 'departments' => $departments, 'test' => $orders[0]->getSchoolclass()->getDepartment()
        ]);
    }

    #[Route('/orderbooks/getDepartment/{departmentId}', name: 'order.getDepartment')]
    public function getDepartment($departmentId, BookOrderRepository $bookOrderRepository, DepartmentRepository $departmentRepository): Response
    {
        $orders = $bookOrderRepository->getOrdersOfDepartment($departmentId);
        $department = $departmentRepository->findOneBy(['id' => $departmentId]);

        $budget = $department->getBudget();
        $usedBudget = $department->getUsedBudget();

        return new JsonResponse(['orders' => $orders, 'budget' => $budget, 'usedBudget' => $usedBudget]);

    }

    #[Route('/orders', name: 'app_orders')]
    public function showOrders()
    {
        return $this->render('order/index.html.twig');

    }

}
