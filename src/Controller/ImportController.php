<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $file = $request->files->get('file'); // get the file from the sent request

        $fileFolder = 'uploads/';

        $filePathName = 'excelUploadFile.xlsx';
        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            dd($e);
        }
        $output = $this->getExcelData($fileFolder . $filePathName);

        return new JsonResponse($output);
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
                    'fullName' => $Row['F'],
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
            $existingBook = $bookRepository->findOneBy(['bnr' => intval($row['bnr'])]);

            if ($existingBook) {
                $book = $existingBook;
            } else {
                $book = new Book();
                $book->setBnr(intval($row['bnr']));
            }

            $subjectRepository = $doctrine->getRepository(Subject::class);
            $existingSubject = $subjectRepository->findOneBy(['fullName' => $row['fullName']]);

            if ($existingSubject){
                $subject=$existingSubject;
            } else{
                $subject = new Subject();
                $subject->setFullName($row['fullName']);

                $entityManager->persist($subject);

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
            $book->setSubject($subject);
            $book->setSchoolGrades($row['schoolGrade']);

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

            if (!$existingBook){
                $entityManager->persist($book);
            }
            $entityManager->flush();
        }


        if (file_exists($filePathName)) {
            unlink($filePathName);
        }

        $url = $this->generateUrl('app_import');
        return new JsonResponse(['url' => $url]);
    }
}
