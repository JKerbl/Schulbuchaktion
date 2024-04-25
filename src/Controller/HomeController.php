<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/home', name: 'home.')]
class HomeController extends AbstractController {
    #[Route('/', name: '')]
    public function index(): Response {
        $results = array(
            "Ingenieur-Mathematik 4.-5. Klasse, HTL",
            "Blattwerk Deutsch - Texte III-V, 3.-4. Klasse, HTL",
            "Best Shots, 3.-5. Klasse, AHS",
            "Recht für Techniker, 4. Klasse, HTL",
            "Z",
            "FKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKS");

            $user = $this->getUser();
        return $this->render('home/index.html.twig', [
            'results' => $results, 'user' => $user
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response {
        $query = $request->get('query', '');

        $results = array(
            "Ingenieur-Mathematik 4.-5. Klasse, HTL",
            "Blattwerk Deutsch - Texte III-V, 3.-4. Klasse, HTL",
            "Best Shots, 3.-5. Klasse, AHS",
            "Recht für Techniker, 4. Klasse, HTL",
            "Z",
            "FKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKSFKS");

        $filteredResults = array_filter($results, function($item) use ($query) {
            return strpos($item, $query) !== false;
        });

        $user = $this->getUser();
        return $this->render('home/index.html.twig', [
            'results' => $filteredResults, 'user' => $user
        ]);
    }
}
