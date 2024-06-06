<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\SchoolClassRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController {
    #[Route('/order/{id}', name: 'order')]
    public function index($id, BookRepository $br, SchoolClassRepository $scr): Response {
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

    #[Route('/orders', name: 'app_orders')]
    public function showOrders()
    {
        return $this->render('order/index.html.twig');
    }
}
