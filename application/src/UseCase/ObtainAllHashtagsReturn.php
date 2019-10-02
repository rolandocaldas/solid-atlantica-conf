<?php
/**
 * Created by PhpStorm.
 * User: rolando
 * Date: 28/11/18
 * Time: 2:44
 */

namespace App\UseCase;

use App\Domain\HashtagCollection;

class ObtainAllHashtagsReturn
{
    private $hashtags;

    public function __construct(HashtagCollection $hashtags)
    {
        $this->hashtags = $hashtags;
    }

    public function hashtags() : HashtagCollection
    {
        return $this->hashtags;
    }
}