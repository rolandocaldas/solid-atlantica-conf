<?php


namespace App\UseCase;


use App\Domain\Entity\HashtagRepository;
use App\Domain\Entity\TweetRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransformTweetsOfHashtagIntoFormat
{
    /**
     * @var HashtagRepository
     */
    private $hashtagRepository;

    /**
     * @var TweetRepository
     */
    private $tweetRepository;

    /**
     * @var TweetsFormatter[]
     */
    private $formaters;

    public function __construct(
        HashtagRepository $hashtagRepository,
        TweetRepository $tweetRepository,
        TweetsFormatter ... $formaters
    )
    {
        $this->hashtagRepository = $hashtagRepository;
        $this->tweetRepository = $tweetRepository;
        $this->processFormaters(... $formaters);
    }

    private function processFormaters(TweetsFormatter ...$formaters) : void
    {
        foreach ($formaters as $formater) {
            $this->formaters[$formater->format()] = $formater;
        }
    }

    public function dispatch(string $type, string $hashtagName)
    {
        if (!array_key_exists($type, $this->formaters)) {
            throw new \Exception("Muerte");
        }

        $hashtag = $this->hashtagRepository->find($hashtagName);
        $tweets = $this->tweetRepository->findBy(['hashtag' => $hashtag]);

        return $this->formaters[$type]->dispatch(... $tweets);
    }
}