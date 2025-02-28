<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\ImportSubjectMap;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use function Sodium\add;
use function Symfony\Component\String\u;

class ImportController extends AbstractController
{
    #[Route('/import', name: 'app_import')]
    public function index(): Response
    {
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }

    /**
     * @Route("/upload-excel", name="xlsx")
     * @param Request $request
     * @throws \Exception
     */
    #[Route('/upload-excel', name: 'xlsx')]
    public function xslx(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $file = $request->files->get('file'); // get the file from the send request
        $fileFolder = 'uploads/';

        $filePathName = 'excelUploadFile.xlsx';
        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            dd($e);
        }
        $output = $this->getShortExcelData($fileFolder . $filePathName);

        return new JsonResponse($output);
    }

    public function getShortExcelData($filePathName): array
    {
        $spreadsheet = IOFactory::load($filePathName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $output = [];
        foreach ($sheetData as $Row) {
            if ($Row['A'] !== null) {
                $output[] = [
                    'bnr' => $Row['A'],
                    'title' => $Row['C'],
                    'schoolGrade' => $Row['G'],
                    'price' => $Row['M'],
                    'ebook' => $Row['P'],
                    'subject' => $Row['F'],
                ];
            }
        }
        return $output;
    }

    #[Route('/map-subjects', name: 'map-subject')]
    public function mapSubjects(EntityManagerInterface $em)
    {
        $spreadsheet = IOFactory::load('uploads/excelUploadFile.xlsx');
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Es werden nur Gegenstände zurückgegeben, die noch kein Mapping haben
        $output = [];

        // Alle Gegenstände aus der CSV
        $csvSubjects = [];

        // Jeden Gegenstand aus der CSV holen
        foreach ($sheetData as $Row) {
            if ($Row['F'] !== "Gegenstand" && !in_array($Row['F'], $csvSubjects)) {
                $csvSubjects[] = $Row['F'];
            }
        }

        foreach ($csvSubjects as $csvSubject) {
            ($subjectMap = $em->getRepository(ImportSubjectMap::class)->findOneBy(['name' => $csvSubject]));

            if ($subjectMap == null) {
                $subjectMap = new ImportSubjectMap();
                $subjectMap->setName($csvSubject);
                $em->persist($subjectMap);
                $em->flush();

                $output[] = $csvSubject;
            }
        }

        return new JsonResponse($output);
    }

    #[Route('/addSubjectMap', name: 'add-subject-map')]
    public function addSubjectMap(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $subjectName = $request->get('subjectName');
        $subjectId = $request->get('subjectId');

        $subjectMap = $em->getRepository(ImportSubjectMap::class)->findOneBy(['name' => $subjectName]);

        if ($subjectMap == null) {
            $subjectMap = new ImportSubjectMap();
            $subjectMap->setName($subjectName);
        }

        $subjectMap->setSubject($em->getRepository(Subject::class)->findOneBy(['id' => $subjectId]));

        $em->persist($subjectMap);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    public function getExcelData($filePathName): array
    {
        $spreadsheet = IOFactory::load($filePathName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $output = [];
        foreach ($sheetData as $Row) {
            if ($Row['A'] !== null) {
                $output[] = [
                    'bnr' => $Row['A'],
                    'shortTitle' => $Row['B'],
                    'title' => $Row['C'],
                    'listType' => $Row['D'],
                    'schoolForm' => $Row['E'],
                    'subject' => $Row['F'],
                    'schoolGrade' => $Row['G'],
                    'teacherVersion' => $Row['H'],
                    'info' => $Row['I'],
                    'price' => $Row['M'],
                    'priceBase' => $Row['N'],
                    'ebookPlusPrice' => $Row['O'],
                    'ebook' => $Row['P'],
                    'ebookPlus' => $Row['Q'],
                ];
            }
        }

        return $output;
    }

    #[Route('/save', name: 'save')]
    public function saveData(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $filePathName = 'uploads/excelUploadFile.xlsx';

        $data = $this->getExcelData($filePathName);

        //Erste Reihe holen
        $firstRow = reset($data);

        // z.B. Preis 2024
        $yearString = $firstRow['price'];

        if (preg_match('/(\d{4})$/', $yearString, $matches)) {
            $year = $matches[1];
        }

        // Erste Reihe entfernen
        array_shift($data);

        $entityManager = $doctrine->getManager();

        foreach ($data as $row) {
            $bookRepository = $doctrine->getRepository(Book::class);
            $existingBook = $bookRepository->findBy(['bnr' => intval($row['bnr']), 'year' => intval($year)]);

            if ($existingBook) {
                $book = $existingBook;
            } else {
                $book = new Book();
                $book->setBnr(intval($row['bnr']));
            }

            // Convert string to integer
            $book->setListType(intval($row['listType']));
            $book->setSchoolForm(intval($row['schoolForm']));
            $book->setYear(intval($year));

            // Convert string to float
            $book->setPriceBase(floatval(str_replace(',', '.', $row['priceBase'])));
            $book->setEbookPlusPrice(floatval(str_replace(',', '.', $row['ebookPlusPrice'])));
            $book->setPrice(floatval(str_replace(',', '.', $row['price'])));

            $book->setShortTitle($row['shortTitle']);
            $book->setTitle($row['title']);
            $book->setInfo($row['info']);
            $book->setSchoolGrades($row['schoolGrade']);

            $book->setImportSubjectMap($doctrine->getRepository(ImportSubjectMap::class)->findOneBy(['name' => $row['subject']]));

            if ($row['teacherVersion']!= null){
                $book->setTeacherVersion(true);
            } else {
                $book->setTeacherVersion(false);
            }

            if ($row['ebook']!= null){
                $book->setEbook(true);
            } else {
                $book->setEbook(false);
            }

            if ($row['ebookPlus']!= null){
                $book->setEbookPlus(true);
            } else {
                $book->setEbookPlus(false);
            }

            $entityManager->persist($book);
        }

        $entityManager->flush();

        if (file_exists($filePathName)) {
            unlink($filePathName);
        }

        $url = $this->generateUrl('app_import');
        return new JsonResponse(['url' => $url]);
    }
}
