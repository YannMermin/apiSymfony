<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    /**
     * Create a client with a default Authorization header.
     */
    /** @test */
    private function createAuthenticatedClient()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneByEmail('y.mermin@test.fr');

        $client->loginUser($user);

        return $client;
    }

    // Test récupérations via API sans JWT token
    public function testGetApiResWithoutToken()
    {

        $client = static::createClient();

        $client->request('GET', '/api/search/brioche');

        $this->assertResponseStatusCodeSame(200);
    }

    // Test récupérations via API sans JWT token
    public function testGetApiResWithToken()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/search/brioche');

        $this->assertResponseStatusCodeSame(200);
    }

    // Ajout produit sans token
    public function testSaveProductWithoutToken()
    {
        $client = static::createClient();

        $client->request('GET', '/api/save/8000500223369');

        $this->assertResponseStatusCodeSame(200);
    }

    // Ajout produit avec token
    public function testSaveProductWithToken()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/save/8000500223369');

        $this->assertResponseStatusCodeSame(200);
    }

    // Retier un produit sans token
    public function testDeleteProductWithoutToken()
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/delete/8000500223369');

        $this->assertResponseStatusCodeSame(200);
    }

    // Retier un produit avec token
    public function testDeleteProductWithToken()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('DELETE', '/api/delete/8000500223369');

        $this->assertResponseStatusCodeSame(200);
    }

    // Vider les produits favoris sans token
    public function testClearProductsWithoutToken()
    {
        $client = static::createClient();

        $client->request('GET', '/api/clear');

        $this->assertResponseStatusCodeSame(200);
    }

    // Vider les produits favoris avec token
    public function testClearProductsWithToken()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/clear');

        $this->assertResponseStatusCodeSame(200);
    }

    // Ajout produit sans token
    public function testExcludeProductWithoutToken()
    {
        $client = static::createClient();

        $client->request('GET', '/api/exclude/8000500295137');

        $this->assertResponseStatusCodeSame(200);
    }

    // Ajout produit avec token
    public function testExcludeProductWithToken()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/exclude/8000500295137');

        $this->assertResponseStatusCodeSame(200);
    }
}
