<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 16:03
 */

namespace App\Domain\Service;

use App\Domain\Entity\Hashtag;

/**
 * Class TwitterSearch
 * @package App\ValueObject
 */
class TwitterSearch
{

    private $hashtag;
    private $includeEntities;
    private $resultType;
    private $count;

    /**
     * TwitterSearch constructor.
     * @param Hashtag $hashtag
     * @param bool $includeEntities
     * @param string $resultType
     * @param int $count
     */
    public function __construct(Hashtag $hashtag, bool $includeEntities, string $resultType, int $count)
    {
        $this->hashtag = $hashtag;
        $this->includeEntities = $includeEntities;
        $this->resultType = $resultType;
        $this->count = $count;
    }

    /**
     * @return Hashtag
     */
    public function hashtag() : Hashtag
    {
        return $this->hashtag;
    }

    /**
     * @return bool
     */
    public function includeEntities(): bool
    {
        return $this->includeEntities;
    }

    /**
     * @return string
     */
    public function resultType(): string
    {
        return $this->resultType;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

}