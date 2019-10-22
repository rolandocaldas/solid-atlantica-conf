<?php


namespace App\UseCase;


use App\Domain\Entity\Tweet;
use Symfony\Component\HttpFoundation\Response;

interface TweetsFormatter
{
    public function format() : string;
    public function dispatch(Tweet ... $tweets) : Response;
}