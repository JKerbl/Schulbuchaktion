<?php

namespace App\Controller;


use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    public function __construct(private int $limit) {}

    private function getMaxPages(BookRepository $bookRepository, int $limit, string $search = null): int
    {
        $totalEntries = $bookRepository->getTotalEntries($search);
        return ceil($totalEntries / $limit);
    }

    private function getCurrentPage(Request $request, int $maxPages): int
    {
        $page = (int) $request->get('page', 1);
        return min(max($page, 1), $maxPages);
    }

    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository, Request $request): Response
    {
        $search = $request->query->get('search', '');
        $maxPages = $this->getMaxPages($bookRepository, $this->limit, $search);
        $currentPage = $this->getCurrentPage($request, $maxPages);

        $books = $bookRepository->getPaginatedEntries($this->limit, $currentPage, $search);

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'maxPages' => $maxPages,
            'currentPage' => $currentPage,
            'search' => $search,
        ]);
    }
}