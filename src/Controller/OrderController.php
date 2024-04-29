<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(): Response {
        $user = $this->getUser();

        return $this->render('order/index.html.twig', [
            'user' => $user,
        ]);
    }
}
