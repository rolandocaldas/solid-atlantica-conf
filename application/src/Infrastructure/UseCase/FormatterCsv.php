<?php
namespace App\Infrastructure\UseCase;

use App\Domain\Entity\Tweet;
use App\UseCase\TweetsFormatter;
use Symfony\Component\HttpFoundation\Response;

class FormatterCsv implements TweetsFormatter
{
    public function format() : string
    {
        return 'csv';
    }

    public function dispatch(Tweet ... $tweets) : Response
    {
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
    }

}