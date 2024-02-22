<?php

namespace App\Tests\Unit\Controller;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Monolog\Test\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class BookEntityTest extends TestCase
{
    public function testCanGetAndSetCategories()  {


        $category = new Category();
        $category->setName("Ficción");
        
        /** @var Author[]|ArrayCollection */
        $categories = new ArrayCollection();

        $categories->add($category);

        $book = new Book();
        $book->addCategory($category);
        $book->addCategory($category);

        self::assertCount($categories->count(), $book->getCategories());
        foreach ($categories as $category) {
            self::assertTrue($book->getCategories()->contains($category));
        }
    }
}
