<?php

namespace App\Event\Book;

use Symfony\Contracts\EventDispatcher\Event;

class BookCreatedEvent extends Event
{
    public const NAME = 'book.created';


    private int $bookId;

    public function __construct(int $bookId = 0)
    {
        $this->bookId = $bookId;
    }

    public function getBookId(): int{
        return $this->bookId;
    }
}
