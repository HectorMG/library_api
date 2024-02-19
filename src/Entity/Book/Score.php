<?php

namespace App\Entity\Book;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Score
{
    #[Column(type: Types::INTEGER)]
    public ?int $value = null;

    public function __construct(int $value = null) {
        self::assertValueIsValid($value);
        $this->value = $value;
    }

    public static function create(?int $value = null) : self {
        return new self($value);
    }

    function getValue() : ?int {
        return $this->value;
    }

    function assertValueIsValid($value)  {
        if ($value===null) {
            return null;
        }

        if ($value<0 || $value>5) {
            throw new \InvalidArgumentException('Value must be between 0 and 5');
        }
    }
}