<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrierungController extends AbstractController
{
    #[Route('/registrierung', name: 'app_registrierung')]
    public function index(): Response
    {
        return $this->render('registrierung/index.html.twig', [
            'controller_name' => 'RegistrierungController',
        ]);
    }
}
