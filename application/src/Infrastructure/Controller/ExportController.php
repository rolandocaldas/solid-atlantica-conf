<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\Persistence\Doctrine\Repository\HashtagRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\TweetRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    /**
     * @Route("/export/{slug}/{type}", name="export_hashtag")
     *
     * @param string $slug
     * @param string $type
     * @param TweetRepository $tweetRepository
     * @param HashtagRepository $hashtagRepository
     * @return Response
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportHashtag(string $slug, string $type, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository): Response
    {
        $hashtag = $hashtagRepository->find($slug);
        $tweets = $tweetRepository->findBy(['hashtag' => $hashtag]);

        if ($type === 'csv') {
            $response = new Response();

            $csvTempFile = fopen('php://memory', 'r+');
            fputcsv($csvTempFile, ['id', 'username', 'user_image', 'content', 'link', 'date']);
            foreach ($tweets AS $tweet) {
                fputcsv($csvTempFile, [$tweet->getTweetId(), $tweet->getUserName(), $tweet->getUserImage(), $tweet->getContent(), 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(), $tweet->getCreatedAt()->format('c')]);
            }
            rewind($csvTempFile);
            $csv_line = stream_get_contents($csvTempFile);
            fclose($csvTempFile);
            $response->setContent($csv_line);
            return $response;
        } else if ($type === 'excel') {
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
}
