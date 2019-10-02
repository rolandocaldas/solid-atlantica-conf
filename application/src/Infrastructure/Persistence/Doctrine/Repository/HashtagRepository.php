<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Entity\Hashtag;
use App\Domain\HashtagCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Hashtag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hashtag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hashtag[]    findAll()
 * @method Hashtag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashtagRepository extends ServiceEntityRepository implements \App\Domain\Entity\HashtagRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Hashtag::class);
    }

    public function findOneByName(string $value) : ? Hashtag
    {
        return $this->findOneBy(['name' => $value]);
    }

    public function save(Hashtag $hashtag, bool $flush = true) : void
    {
        $this->_em->persist($hashtag);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function loadAllAndReturn() : HashtagCollection
    {
        return new HashtagCollection(... $this->findAll());
    }
}
