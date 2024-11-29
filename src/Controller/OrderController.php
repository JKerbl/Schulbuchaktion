<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\Department;
use App\Entity\Subject;
use App\Repository\BookOrderRepository;
use App\Repository\BookRepository;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use App\Repository\SubjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController {
    #[Route('/order/{id}', name: 'order')]
    public function order($id, BookRepository $br, SchoolClassRepository $scr, SubjectRepository $sr): Response {
        $res = array();
        $books = $br->findAll();
        foreach ($books as $key => $book) {
            $res[] = array(
                'id' => $book->getId(),
                'shortTitle' => $book->getShortTitle()
            );
        }
        $user = $this->getUser();
        $subjects = $sr->findAll();
        $userSubject = $sr->findSubjectsByHeadOfSubjectId($user->getId());

        $book = $br->find($id);
        $classes = $scr->findAlLByYear(date('Y'));

        if ($classes === []) {
            return $this->render('home/index.html.twig', [
                'results' => $res, 'user' => $user,
                'searchInput' => "",
            ]);
        }

        return $this->render('order/index.html.twig', [
            'user' => $user, 'book' => $book,
            'classes' => $classes,'allSubjects' => $subjects,
            'userSubject' => $userSubject,
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

    private function getMaxPages(BookOrderRepository $bookOrderRepository, int $limit, int $year, int $department = null, int $grade = null, string $search = null): int
    {
        $totalEntries = $bookOrderRepository->getTotalEntries($year, $department, $grade, $search);
        return ceil($totalEntries / $limit);
    }

    private function getCurrentPage(Request $request, int $maxPages): int
    {
        $page = (int) $request->get('page', 1);
        return min(max($page, 1), $maxPages);
    }

    #[Route('/orderbooks/index', name: 'order.index')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $bookOrderRepository = $em->getRepository(BookOrder::class);
        $limit = $this->getUser()->getPagelimit();

        // get requested or current (as default) year and all years
        $year = $request->query->get('year', date('Y'));
        $allYears = $em->getRepository(Department::class)->findAllYears();

        $departmentFilter = $request->query->get('department', null);
        if ($departmentFilter == 0) {
            // 0 is the value for the "all" option
            $departmentFilter = null;
        }

        $gradeFilter = $request->query->get('grade', null);
        if ($gradeFilter == 0) {
            // 0 is the value for the "all" option
            $gradeFilter = null;
        }

        // get all departments and orders of the current year
        $departments = $em->getRepository(Department::class)->findAllByYear($year);

        // get the search query
        $search = $request->query->get('search', '');

        $maxPages = $this->getMaxPages($bookOrderRepository, $limit, $year, $departmentFilter, $gradeFilter);
        $currentPage = $this->getCurrentPage($request, $maxPages);

        $orders = $bookOrderRepository->getPaginatedEntries($limit, $currentPage, $year, $departmentFilter, $gradeFilter, $search);

        return $this->render('order/overview.html.twig', [
            'departments' => $departments,
            'orders' => $orders,
            'currentYear' => $year,
            'allYears' => $allYears,
            'departmentFilter' => $departmentFilter ?? 0,
            'gradeFilter' => $gradeFilter ?? 0,
            'maxPages' => $maxPages,
            'currentPage' => $currentPage,
            'search' => $search,
        ]);
    }

    #[Route('/orderbooks/delete/{id}', name: 'order.delete')]
    public function delete($id, EntityManagerInterface $entityManager, BookOrderRepository $bookOrderRepository): Response
    {
        $bookOrder = $bookOrderRepository->findOneBy(['id' => $id]);

        $book = $bookOrder->getBook();

        $schoolClass = $bookOrder->getSchoolclass();

        $department = $schoolClass->getDepartment();

        $schoolClass->setUsedBudget($schoolClass->getUsedBudget()-$bookOrder->getCount()*$book->getPrice());
        $department->setUsedBudget($department->getUsedBudget()-$bookOrder->getCount()*$book->getPrice());

        $entityManager->remove($bookOrder);
        $entityManager->flush();

        return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order', name: 'submit_order', methods: ['POST'])]
    public function submitOrder(DepartmentRepository $departmentRepository, Request $request, EntityManagerInterface $em, BookRepository $br, SchoolClassRepository $scr): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $classId = $data['classId'];
            $bookId = $data['bookId'];
            $bookAmount = $data['bookAmount'];
            $teacherCopy = $data['teacherCopy'];
            $ebookPlus = $data['ebookPlus'];
            $ebook = $data['ebook'];
            $subjectId = $data['subject'];

            $class = $scr->find($classId);
            $book = $br->find($bookId);
            $department = $departmentRepository->find($class->getDepartment()->getId());
            $subject = $em->getRepository(Subject::class)->find($subjectId);

            $class->setUsedBudget($class->getUsedBudget()+$bookAmount*$book->getPrice());
            $department->setUsedBudget($department->getUsedBudget()+$bookAmount*$book->getPrice());


            if (!$class || !$book) {
                return new JsonResponse(['success' => false, 'message' => 'Klasse oder Buch nicht gefunden.'], 404);
            }

            $bookOrder = new BookOrder();
            $bookOrder->setSchoolClass($class);
            $bookOrder->setBook($book);
            $bookOrder->setCount($bookAmount);
            $bookOrder->setTeacherCopy($teacherCopy);
            $bookOrder->setEBook($ebook);
            $bookOrder->setEBookPlus($ebookPlus);

            $bookOrder->setSubject($subject);
            $subject->addBookOrder($bookOrder);

            $em->persist($bookOrder);
            $em->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()], 500);
        }
    }
}
