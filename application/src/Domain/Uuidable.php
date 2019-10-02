<?php
/**
 * Created by PhpStorm.
 * User: rolando.caldas
 * Date: 13/06/2018
 * Time: 13:39
 */

namespace App\Domain;

use Ramsey\Uuid\Uuid;

trait Uuidable
{
    private $id;

    private function generateId()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }
}