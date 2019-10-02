<?php

namespace App\Infrastructure\Command;

use App\Domain\Entity\Hashtag;
use App\Domain\Entity\Tweet;
use App\Domain\Service\TwitterSearch;
use App\Infrastructure\Persistence\Doctrine\Repository\HashtagRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\TweetRepository;
use App\Infrastructure\Service\OutputLogger;
use App\Infrastructure\Service\TwitterClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TweetFetchCommand extends Command
{
    protected static $defaultName = 'tweet:fetch';

    private $twitterClient;
    private $tweetRepository;
    private $hashtagRepository;
    private $logger;

    public function __construct(TwitterClient $twitterClient, TweetRepository $tweetRepository, HashtagRepository $hashtagRepository, LoggerInterface $logger)
    {
        $this->twitterClient = $twitterClient;
        $this->tweetRepository = $tweetRepository;
        $this->hashtagRepository = $hashtagRepository;
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Fetch and persist tweets')
            ->addArgument('text', InputArgument::REQUIRED, 'Text to search')
            ->addArgument('count', InputArgument::OPTIONAL, 'Max. number of tweets to fetch', 4)
            ->addArgument(
                'result_type',
                InputArgument::OPTIONAL,
                <<<'EOD'
Specifies what type of search results you would prefer to receive. The current default is "mixed." Valid values include:

- mixed : Include both popular and real time results in the response.

- recent : return only the most recent results in the response

- popular : return only the most popular results in the response.

EOD
                ,
                'recent'
            )
            ->addOption('include-entities', 'i', InputOption::VALUE_NONE, 'Include entities in twitter fetch')
            ->addOption('no-persist', null, InputOption::VALUE_NONE, 'Disable tweet persistence');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new OutputLogger($input, $output, $this->logger);
        $persist = !$input->getOption('no-persist');

        $twitterSearch = $this->processInputAndReturnTwitterSearch($input, $persist);

        $io->title(sprintf('Searching tweets for: %s', $twitterSearch->hashtag()->getName()));

        $tweets = $this->buildTweets(
            $this->twitterClient->findTweetsWith($twitterSearch),
            $twitterSearch->hashtag(),
            $io
        );

        $this->persistDataIfIsEnabled($persist, $io, $twitterSearch->hashtag(), ... $tweets);
        $this->showTweets($io, ... $tweets);

        $io->success('Operation finished!');
    }

    private function processInputAndReturnTwitterSearch(InputInterface $input, bool $persist) : TwitterSearch
    {
        return new TwitterSearch(
            $this->obtainHashtagFromText($input->getArgument('text'), $persist),
            (bool) $input->getOption('include-entities'),
            (string) $input->getArgument('result_type'),
            (int) $input->getArgument('count')
        );
    }

    private function obtainHashtagFromText(string $text, bool $persist) : Hashtag
    {
        $hashtag = $this->hashtagRepository->findOneByName($text);
        return !empty($hashtag) ? $hashtag : $this->constructHashtagAndReturn($text, $persist);
    }

    private function constructHashtagAndReturn(string $text, bool $persist) : Hashtag
    {
        $hashtag = Hashtag::fromName($text);
        if ($persist === true) {
            $this->hashtagRepository->save($hashtag);
        }
        return $hashtag;
    }

    private function buildTweets(array $tweets, Hashtag $hashtag, OutputLogger $io) : array
    {
        if (empty($tweets)) {
            $io->error('No tweets found!');
            return [];
        }

        $tweetsToSave = [];

        foreach ($tweets AS $tweet) {
            $tweetsToSave[] = Tweet::buildAndAttachToHashtag($tweet, $hashtag);
        }

        return $tweetsToSave;
    }

    private function persistDataIfIsEnabled(bool $persist, OutputLogger $io, Hashtag $hashtag, Tweet ... $tweets) : void
    {
        if (!$persist) {
            return;
        }

        $this->updateHashtagLastTweet($hashtag, ... $tweets);
        $this->saveTweets(... $tweets);
        $io->note(sprintf('Saved %d tweets!', count($tweets)));
    }

    private function updateHashtagLastTweet(Hashtag $hashtag, Tweet ...$tweets)
    {
        $lastTweetId = $this->obtainMaxTweetId(...$tweets);

        if ($lastTweetId > $hashtag->getLastTweet()) {
            $hashtag->setLastTweet($lastTweetId);
        }

        $this->hashtagRepository->save($hashtag);
    }

    private function obtainMaxTweetid(Tweet ... $tweets) : int
    {
        $ids = [];
        foreach ($tweets AS $tweet) {
            $ids[] = (int) $tweet->getTweetId();
        }
        $ids[] = 0;
        return max($ids);
    }

    private function saveTweets(Tweet ...$tweets)
    {
        if (empty($tweets)) {
            return;
        }

        $this->tweetRepository->multipleSave(... $tweets);
    }

    private function showTweets(OutputLogger $io, Tweet ...$tweets) : void
    {
        if (empty($tweets)) {
            return;
        }

        foreach ($tweets as $index => $tweet) {
            $io->section(sprintf('Tweet #%d', $index + 1));
            $io->writeln(sprintf(
                '%s by @%s',
                $tweet->getContent(),
                $tweet->getUserName()
            ));
        }
    }
}
