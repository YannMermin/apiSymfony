<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Product
{
    private $client;
    private $userRepository;

    private const URL_API_SEARCH = 'https://fr.openfoodfacts.org/cgi/search.pl?';
    private const NB_PER_PAGE = 20;

    public function __construct(HttpClientInterface $client, UserRepository $userRepository)
    {
        $this->client = $client;
        $this->userRepository = $userRepository;
    }

    // Récupération des produits via une recherche par mots
    public function fetchProductsApi($searchTerms, User $user)
    {
        $response = $this->client->request('GET', $this::URL_API_SEARCH . 'search_terms=' . $searchTerms . "&search_simple=1&action=process&json=1");

        if (is_array($response->toArray())) {

            if (key_exists('products', $response->toArray())) {

                $resApi = $response->toArray()['products'];

                if (is_array($resApi)) {

                    $resTab = [];

                    foreach ($resApi as $product) {

                        if (is_array($product) && key_exists('product_name_fr', $product)) {

                            $resTab[] = [
                                'name' => $product['product_name_fr'],
                                'ean' => $product['code'],
                                'brand' => $product['brands'],
                                'ingredients' => $product['ingredients'],
                                'allergens' => $product['allergens'],
                                'nutriscore' => $product['nutriscore_grade'],
                                'nutritionalValues' => $product['nutriments'],
                                'substitutes' => $product['compared_to_category'],
                                'pertinence' => $product['rev']
                            ];
                        }
                    }

                    if (count($resTab) > 0) {

                        // On retire les produits qui font partie de la liste des produits exclus de l'utilisateur

                        if ($user->getExcludedProducts()->count() > 0) {
                            foreach ($user->getExcludedProducts() as $exluded) {
                                foreach ($resTab as $key => $product) {
                                    if ($product['ean'] === $exluded->getEan()) {
                                        array_splice($resTab, $key);
                                    }
                                }
                            }
                        }

                        // On ordonne les résultats par le champ "rev" (pertinence)

                        usort($resTab, function ($a, $b) {
                            return strcmp($b["pertinence"], $a["pertinence"]);
                        });

                        // On pagine par lot de 20

                        $resPaginated = [];
                        $indexPage = 1;
                        $indexProductPage = 0;

                        foreach ($resTab as $product) {

                            if ($indexProductPage >= $this::NB_PER_PAGE) {
                                $indexPage++;
                                $indexProductPage = 0;

                                $resPaginated[$indexPage] = [];
                            }

                            $resPaginated[$indexPage][$indexProductPage] = $product;

                            $indexProductPage++;
                        }

                        dd($resPaginated);

                        return $resPaginated;
                    }
                }
            }
        }

        return null;
    }

    // Enregistrer un produit dans les favoris de l'utilisateur
    public function saveProductUserFavorites($codeEan, User $user)
    {
        $response = $this->client->request('GET', $this::URL_API_SEARCH . 'code=' . $codeEan . "&search_simple=1&action=process&json=1");

        if (is_array($response->toArray())) {

            if (key_exists('products', $response->toArray())) {

                $resApi = $response->toArray()['products'];

                if (is_array($resApi)) {

                    $productDatas = [];

                    if (is_array($resApi[0]) && key_exists('product_name_fr', $resApi[0])) {

                        $productDatas = [
                            'name' => $resApi[0]['product_name_fr'],
                            'ean' => $resApi[0]['code'],
                            'brand' => $resApi[0]['brands'],
                            'ingredients' => $resApi[0]['ingredients'],
                            'allergens' => $resApi[0]['allergens'],
                            'nutriscore' => $resApi[0]['nutriscore_grade'],
                            'nutritionalValues' => $resApi[0]['nutriments'],
                            'substitutes' => $resApi[0]['compared_to_category'],
                            'pertinence' => $resApi[0]['rev']
                        ];
                    }

                    if (count($productDatas) > 0) {

                        return $this->userRepository->addFavorite($productDatas, $user);
                    }
                }
            }
        }

        return false;
    }

    // Retire un produit des favoris de l'utilisateur
    public function removeProductUserFavorites($codeEan, User $user)
    {
        return $this->userRepository->removeFavorite($codeEan, $user);
    }

    // Retire l'ensembles des favoris de l'utilisateur
    public function clearProductUserFavorites(User $user)
    {
        return $this->userRepository->clearFavorites($user);
    }

    // Enregistrer un produit dans la liste des produits exclus de l'utilisateur
    public function excludeProductUser($codeEan, User $user)
    {
        $response = $this->client->request('GET', $this::URL_API_SEARCH . 'code=' . $codeEan . "&search_simple=1&action=process&json=1");

        if (is_array($response->toArray())) {

            if (key_exists('products', $response->toArray())) {

                $resApi = $response->toArray()['products'];

                if (is_array($resApi)) {

                    $productDatas = [];

                    if (is_array($resApi[0]) && key_exists('product_name_fr', $resApi[0])) {

                        $productDatas = [
                            'name' => $resApi[0]['product_name_fr'],
                            'ean' => $resApi[0]['code'],
                            'brand' => $resApi[0]['brands'],
                            'ingredients' => $resApi[0]['ingredients'],
                            'allergens' => $resApi[0]['allergens'],
                            'nutriscore' => $resApi[0]['nutriscore_grade'],
                            'nutritionalValues' => $resApi[0]['nutriments'],
                            'substitutes' => $resApi[0]['compared_to_category'],
                            'pertinence' => $resApi[0]['rev']
                        ];
                    }

                    if (count($productDatas) > 0) {

                        return $this->userRepository->addExcluded($productDatas, $user);
                    }
                }
            }
        }

        return false;
    }
}
