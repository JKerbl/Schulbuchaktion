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

        $filePathName = md5(uniqid()) . $file->getClientOriginalName();
        // apply md5 function to generate an unique identifier for the file and concat it with the file extension
        try {
            $file->move($fileFolder, $filePathName);
        } catch (FileException $e) {
            dd($e);
        }
        $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file


        //$row = $spreadsheet->getActiveSheet()->removeRow(1);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
        //dd($sheetData);
        $entityManager = $doctrine->getManager();
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

        return new JsonResponse($output);
        //return $this->json('excel scanned', 200);
        //return $this->redirectToRoute('home');
    }
}
