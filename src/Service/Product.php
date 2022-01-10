<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Product
{
    private $client;

    private const URL_API_SEARCH = 'https://fr.openfoodfacts.org/cgi/search.pl?search_terms=';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchProductsApi($searchTerms)
    {
        $response = $this->client->request('GET', $this::URL_API_SEARCH . $searchTerms . "&search_simple=1&action=process&json=1");

        if (is_array($response->toArray())) {
            if (key_exists('products', $response->toArray())) {
                return $response->toArray()['products'];
            }
        }
    }
}
