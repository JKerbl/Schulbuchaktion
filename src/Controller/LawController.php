<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LawController extends AbstractController
{
    #[Route('/imprint', name: 'app_imprint')]
    public function imprint(): Response
    {
        return $this->render('law/imprint.html.twig');
    }

    #[Route('/privacy-policy', name: 'app_privacy_policy')]
    public function index(): Response
    {
        return $this->render('law/privacy_policy.html.twig');
    }
}
