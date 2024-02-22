<?php

namespace App\Tests\Unit\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Entity\User;
use App\Model\Comment\CommentRepositoryCriteria;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
{
    public function testCreateCategoryEmpty()
    {
        $client = static::createClient();
        $this->sendRequest($client, ["name" => ""], "POST");
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreateCategorySuccess()
    {
        $client = static::createClient();
        $this->sendRequest($client, ["name" => "Romance"], "POST");
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    private function sendRequest(KernelBrowser $client, array $json, string $method)
    {
        $client->request(
            $method,
            '/api/category',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => 'LIBRARIFY'
            ],
            json_encode($json)
        );
    }
}
