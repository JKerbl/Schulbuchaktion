<?php

namespace App\Controller;

use App\Entity\BookOrder;
use App\Entity\Department;
use App\Entity\Subject;
use App\Form\BookOrderType;
use App\Repository\BookOrderRepository;
use App\Repository\BookRepository;
use App\Repository\DepartmentRepository;
use App\Repository\SchoolClassRepository;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use function PHPUnit\Framework\isEmpty;

#[Route('/orderBook')]
class OrderController extends AbstractController {
    #[Route('/', name: 'orderBook')]
    public function order(BookRepository $br, SchoolClassRepository $scr, SubjectRepository $sr, Request $request): Response {
        $id = $request->query->get('id', null);

        if ($id === null) {
            return $this->redirectToRoute('app_book_index');
        }

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
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('order/index.html.twig', [
            'user' => $user,
            'book' => $book,
            'classes' => $classes,
            'allSubjects' => $subjects,
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
        return min(max($page, 0), $maxPages);
    }

    #[Route('/index', name: 'order.index')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $user = $this->getUser();
        $bookOrderRepository = $em->getRepository(BookOrder::class);
        $limit = $this->getUser()->getPagelimit();

        // Get the sorting settings
        $sortDirection = $request->query->get('sortDirection', 'ASC');
        $sortBy = $request->query->get('sortBy', 'class');

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

        $orders = $bookOrderRepository->getPaginatedEntries($limit, $currentPage, $year, $departmentFilter, $gradeFilter, $search, $sortDirection, $sortBy);

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
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'user' => $user
        ]);
    }

    #[Route('/delete/{id}', name: 'order.delete')]
    public function delete($id, EntityManagerInterface $entityManager, BookOrderRepository $bookOrderRepository): Response
    {
        $bookOrder = $bookOrderRepository->findOneBy(['id' => $id]);

        $book = $bookOrder->getBook();

        $schoolClass = $bookOrder->getSchoolclass();

        $department = $schoolClass->getDepartment();

        $orderFor = $bookOrder->getOrderFor();

        if ($orderFor == 'Mit Repetenten'){
            $bookAmount = $schoolClass->getStudentsAmount();
        } else if ($orderFor == 'Ohne Repetenten'){
            $bookAmount = $schoolClass->getStudentsAmount();
        } else if ($orderFor == 'Nur Repetenten'){
            $bookAmount = 0;
        } else {
            $bookAmount = 0;
        }

        $schoolClass->setUsedBudget($schoolClass->getUsedBudget() - $bookAmount * $book->getPrice());
        $department->setUsedBudget($department->getUsedBudget() - $bookAmount * $book->getPrice());

        $entityManager->remove($bookOrder);
        $entityManager->flush();

        return $this->redirectToRoute('order.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/edit/{id}', name: 'app_book_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BookOrder $bookOrder, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookOrderType::class, $bookOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('orderBook', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'book_order' => $bookOrder,
            'form' => $form,
        ]);
    }

    #[Route('/order', name: 'submit_order', methods: ['POST'])]
    public function submitOrder(DepartmentRepository $departmentRepository, Request $request, EntityManagerInterface $em, BookRepository $br, SchoolClassRepository $scr): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $classId = $data['classId'];
            $bookId = $data['bookId'];
            $orderFor = $data['orderFor'];
            $teacherCopy = $data['teacherCopy'];
            $ebookPlus = $data['ebookPlus'];
            $ebook = $data['ebook'];
            $subjectId = $data['subject'];

            $class = $scr->find($classId);
            $book = $br->find($bookId);
            $department = $departmentRepository->find($class->getDepartment()->getId());
            $subject = $em->getRepository(Subject::class)->find($subjectId);

            // Repetenten sollen nicht ins Budget einfließen
            if ($orderFor == 'Mit Repetenten'){
                $bookAmount = $class->getStudentsAmount() + $class->getRepAmount();
            } else if ($orderFor == 'Ohne Repetenten'){
                $bookAmount = $class->getStudentsAmount();
            } else if ($orderFor == 'Nur Repetenten'){
                $bookAmount = 0;
            } else {
                $bookAmount = 0;
            }

            $class->setUsedBudget($class->getUsedBudget()+ $bookAmount * $book->getPrice());
            $department->setUsedBudget($department->getUsedBudget()+$bookAmount*$book->getPrice());


            if (!$class || !$book) {
                return new JsonResponse(['success' => false, 'message' => 'Klasse oder Buch nicht gefunden.'], 404);
            }

            $bookOrder = new BookOrder();
            $bookOrder->setSchoolClass($class);
            $bookOrder->setBook($book);
            $bookOrder->setOrderFor($orderFor);
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
