<?php
namespace App\Infrastructure\UseCase;

use App\Domain\Entity\Tweet;
use App\UseCase\TweetsFormatter;
use Symfony\Component\HttpFoundation\Response;

class FormatterJson implements TweetsFormatter
{
    public function format() : string
    {
        return 'json';
    }

    public function dispatch(Tweet ... $tweets) : Response
    {
        foreach ($tweets AS $tweet) {
            $data[] = [
                'id' => $tweet->getTweetId(),
                'username' => $tweet->getUserName(),
                'user_image' => $tweet->getUserImage(),
                'content' => $tweet->getContent(),
                'link' => 'https://twitter.com/' . $tweet->getUserName() . '/status/' . $tweet->getTweetId(),
                'date' => $tweet->getCreatedAt()->format('c')
            ];
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        return $response;
    }

}




