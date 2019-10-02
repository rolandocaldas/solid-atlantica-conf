<?php

namespace App\Infrastructure\Controller;

use App\Infrastructure\Persistence\Doctrine\Repository\HashtagRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\TweetRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RandomTweetController
{
    /**
     * @Route("/hashtag/{slug}", name="random_tweet")
     *
     * @param string $slug
     * @param TweetRepository $tweetRepository
     * @param HashtagRepository $hashtagRepository
     * @param \Twig_Environment $twig
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getRandomTweet(string $slug, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository, \Twig_Environment $twig)
    {
        $hashtag = $hashtagRepository->find($slug);

        return (new Response())->setContent($twig->render('default/randomTweet/winner.html.twig', [
            'hashtag' => $hashtag,
            'winner' => $tweetRepository->getRandomTweet($hashtag),
        ]));
    }
}
