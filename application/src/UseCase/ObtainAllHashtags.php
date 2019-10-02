<?php
/**
 * Created by PhpStorm.
 * User: rolando
 * Date: 28/11/18
 * Time: 2:40
 */

namespace App\UseCase;

use App\Domain\Entity\HashtagRepository;

class ObtainAllHashtags
{
    private $hashtagRepository;

    public function __construct(HashtagRepository $hashtagRepository)
    {
        $this->hashtagRepository = $hashtagRepository;
    }

    public function dispatch() : ObtainAllHashtagsReturn
    {
        return new ObtainAllHashtagsReturn($this->hashtagRepository->loadAllAndReturn());
    }
}