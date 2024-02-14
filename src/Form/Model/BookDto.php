<?php

namespace App\Form\Model;

use App\Entity\Book;

class BookDto
{
    public string $title;
    public string $base64Image;
    public $categories;


    public function __construct() {
        $this->categories = [];
    }

    public static function createFromBook(Book $book) {
        $dto = new self();
        $dto->title = $book->getTitle() ?? '';
        return $dto;
    }
}