<?php
/**
 * Created by PhpStorm.
 * User: rolando
 * Date: 28/11/18
 * Time: 2:14
 */

namespace App\Domain\Entity;

use App\Domain\HashtagCollection;

interface HashtagRepository
{
    public function loadAllAndReturn() : HashtagCollection;
    public function save(Hashtag $hashtag, bool $flush = true) : void;
}