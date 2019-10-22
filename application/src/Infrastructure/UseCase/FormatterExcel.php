<?php

namespace App\Infrastructure\UseCase;

use App\Domain\Entity\Tweet;
use App\UseCase\TweetsFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormatterExcel implements TweetsFormatter
{
    public function format() : string
    {
        return 'excel';
    }

    public function dispatch(Tweet ... $tweets) : Response
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
        $worksheet = new Worksheet();
        $spreadsheet->addSheet($worksheet);
        $i = 1;
        $worksheet->setCellValue('A' . $i, 'id');
        $worksheet->setCellValue('B' . $i, 'username');
        $worksheet->setCellValue('C' . $i, 'user_image');
        $worksheet->setCellValue('D' . $i, 'content');
        $worksheet->setCellValue('E' . $i, 'link');
        $worksheet->setCellValue('F' . $i, 'date');
        $i++;
        foreach ($tweets AS $tweet) {
            $worksheet->setCellValue('A' . $i, $tweet->getTweetId());
            $worksheet->setCellValue('B' . $i, $tweet->getUserName());
            $worksheet->setCellValue('C' . $i, $tweet->getUserImage());
            $worksheet->setCellValue('D' . $i, $tweet->getContent());
            $worksheet->setCellValue('E' . $i, 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId());
            $worksheet->setCellValue('F' . $i, $tweet->getCreatedAt()->format('c'));

            $worksheet->getColumnDimension('A')->setAutoSize(true);
            $worksheet->getColumnDimension('B')->setAutoSize(true);
            $worksheet->getColumnDimension('C')->setAutoSize(true);
            $worksheet->getColumnDimension('D')->setAutoSize(true);
            $worksheet->getColumnDimension('E')->setAutoSize(true);
            $worksheet->getColumnDimension('F')->setAutoSize(true);
            $i++;
        }
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="hashtag-' . (new \DateTimeImmutable())->format('YmdHis') . '.xlsx"');
        $response->setCallback(static function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        return $response->send();
    }

}