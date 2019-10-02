<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Hashtag;
use App\Domain\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tweet[]    findAll()
 * @method Tweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TweetRepository extends ServiceEntityRepository implements \App\Domain\Entity\TweetRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    public function getRandomTweet(?Hashtag $hashtag) : ?Tweet
    {
        $tweets = $this->findBy(['hashtag' => $hashtag]);
        shuffle($tweets);
        return $tweets[0];
    }

    public function save(Tweet $tweet, bool $flush = true) : void
    {
        $this->_em->persist($tweet);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function multipleSave(Tweet ...$tweets) : void
    {
        foreach ($tweets as $tweet) {
            $this->save($tweet, false);
        }
        $this->_em->flush();
    }
}
