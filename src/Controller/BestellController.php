<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BestellController extends AbstractController
{
    #[Route('/bestell', name: 'app_bestell')]
    public function index(): Response
    {
        return $this->render('bestell/index.html.twig', [
            'controller_name' => 'BestellController',
        ]);
    }
}
