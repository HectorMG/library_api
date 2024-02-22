<?php

namespace App\Tests\Unit\Login;

use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IsbnControllerTest extends WebTestCase
{
    public function testRouteAccordingToRoleMember() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('hector@gmail.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/dashboard');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testRouteAccordingToRoleAdmin() {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@gmail.com');

        $client->loginUser($testUser);

        $client->request('GET', '/admin/dashboard');
        $this->assertResponseIsSuccessful();
    }
}
