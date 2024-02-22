<?php

namespace App\Model\Comment;

class CommentRepositoryCriteria
{
    public function __construct(
        public readonly ?string $orderBy = 'DESC',
        public readonly ?int $book = null
    ) {
    }
}
