<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response{
        $results = array(
            "Ingenieur-Mathematik 4.-5. Klasse, HTL",
            "Blattwerk Deutsch - Texte III-V, 3.-4. Klasse, HTL",
            "Best Shots, 3.-5. Klasse, AHS",
            "Recht für Techniker, 4. Klasse, HTL",
            "Z",
            "FKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKS"
        );


        return $this->render('home/index.html.twig', [
            'results' => $results,
        ]);
    }
}
