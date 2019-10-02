<?php
/**
 * Created by PhpStorm.
 * User: rolando
 * Date: 28/11/18
 * Time: 2:45
 */

namespace App\Domain;

use App\Domain\Entity\Hashtag;

class HashtagCollection implements \Countable, \Iterator
{
    private $position;
    private $data;

    public function __construct(?Hashtag ... $hashtags)
    {
        $this->position = 0;
        $this->data = empty($hashtags) ? [] : $hashtags;
    }

    public function count()
    {
        return count($this->data);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }

}