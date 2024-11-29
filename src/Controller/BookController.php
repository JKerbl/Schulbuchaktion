<?php

namespace App\Controller;


use App\Entity\Subject;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    public function __construct()
    {
    }

    private function getMaxPages(BookRepository $bookRepository, int $limit, int $year, int $subject = null, int $grade = null, string $search = null): int
    {
        $totalEntries = $bookRepository->getTotalEntries($year, $subject, $grade, $search);
        return ceil($totalEntries / $limit);
    }

    private function getCurrentPage(Request $request, int $maxPages): int
    {
        $page = (int) $request->get('page', 1);
        return min(max($page, 0), $maxPages);
    }

    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em, BookRepository $bookRepository, Request $request): Response
    {
        $limit = $this->getUser()->getPagelimit();

        // get requested or current (as default) year and all years
        $year = $request->query->get('year', date('Y'));
        $allYears = $bookRepository->getAllYears();

        // get the subject and grade filters
        $subjectFilter = $request->query->get('subject', null);
        if ($subjectFilter == 0){
            // 0 is the value for the "all" option
            $subjectFilter = null;
        }

        $gradeFilter = $request->query->get('grade', null);
        if ($gradeFilter == 0){
            // 0 is the value for the "all" option
            $gradeFilter = null;
        }

        // get all subjects for the filter
        $subjects = $em->getRepository(Subject::class)->findAll();


        $search = $request->query->get('search', '');
        $maxPages = $this->getMaxPages($bookRepository, $limit, $year, $subjectFilter, $gradeFilter, $search);
        $currentPage = $this->getCurrentPage($request, $maxPages);

        $books = $bookRepository->getPaginatedEntries($limit, $currentPage, $year, $subjectFilter, $gradeFilter, $search);


        return $this->render('book/index.html.twig', [
            'books' => $books,
            'maxPages' => $maxPages,
            'currentPage' => $currentPage,
            'search' => $search,
            'subjectFilter' => $subjectFilter ?? 0,
            'gradeFilter' => $gradeFilter ?? 0,
            'currentYear' => $year,
            'allYears' => $allYears,
            'subjects' => $subjects
        ]);
    }
}