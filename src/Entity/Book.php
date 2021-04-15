<?php

namespace App\Entity;

class Book
{
    private $id;

    public function __construct(int $id)
    {
        $this->setId($id);
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}