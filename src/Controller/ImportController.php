<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
                    'info' => $Row['I'],
                    'price' => $Row['N'] == null ? $Row['M'] : $Row['N'],
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

        /*
        $entityManager = $doctrine->getManager();

        foreach ($data as $row) {
            $entity = new YourEntity();
            $entity->setBnr($row['bnr']);
            $entity->setShortTitle($row['shortTitle']);
            $entity->setTitle($row['title']);
            $entity->setListType($row['listType']);
            $entity->setSchoolForm($row['schoolForm']);
            $entity->setFullName($row['fullName']);
            $entity->setSchoolGrade($row['schoolGrade']);
            $entity->setInfo($row['info']);
            $entity->setPrice($row['price']);
            $entity->setEbookPlusPrice($row['ebookPlusPrice']);
            $entity->setEbook($row['ebook']);
            $entity->setEbookPlus($row['ebookPlus']);

            $entityManager->persist($entity);
        }

        $entityManager->flush();
        */

        if (file_exists($filePathName)) {
            unlink($filePathName);
        }

        $url = $this->generateUrl('app_import');
        return new JsonResponse(['url' => $url]);
    }
}
