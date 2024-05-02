<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/home', name: 'home.')]
class HomeController extends AbstractController {
    #[Route('/', name: '')]
    public function index(BookRepository $br): Response {
        $res = array();
        $books = $br->findAll();

        foreach ($books as $key => $book) {
            $res[] = $book->getShortTitle();
        }

        $user = $this->getUser();
        return $this->render('home/index.html.twig', [
            'results' => $res, 'user' => $user
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, BookRepository $br): Response {
        $query = $request->get('query', '');

        $res = array();
        $books = $br->findAll();

        foreach ($books as $key => $book) {
            $res[] = $book->getShortTitle();
        }

        $filteredResults = array_filter($res, function($item) use ($query) {
            return strpos($item, $query) !== false;
        });

        $user = $this->getUser();
        return $this->render('home/index.html.twig', [
            'results' => $filteredResults, 'user' => $user
        ]);
    }
}
