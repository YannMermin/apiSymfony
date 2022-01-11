<?php

namespace App\Controller;

use App\Service\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiProductController extends AbstractController
{
    private $productSrvc;
    private $serializer;

    public function __construct(Product $productSrvc, SerializerInterface $serializer)
    {
        $this->productSrvc = $productSrvc;
        $this->serializer = $serializer;
    }

    #[Route('/search/{searchTerms}', name: 'search_product', methods: ['GET'])]
    public function searchProduct(Request $request)
    {
        if ($request->get('searchTerms') && $request->get('searchTerms') !== '') {
            $products = $this->productSrvc->fetchProductsApi($request->get('searchTerms'));

            if ($products) {
                return $this->json([
                    'products' => $this->serializer->serialize($products, 'json')
                ]);
            }

            return $this->json([
                'message' => 'Aucun résultat à votre recherche.',
            ]);
        }

        return $this->json([
            'message' => 'Aucune recherche précisée.',
        ], 400);
    }

    #[Route('/save/{codeEan}', name: 'save_favorite', methods: ['GET'])]
    public function saveProductFavorite(Request $request)
    {
        if ($request->get('codeEan') && $request->get('codeEan') !== '') {

            $saveProduct = $this->productSrvc->saveProductUserFavorites($request->get('codeEan'), $this->getUser());

            if ($saveProduct) {
                return $this->json([
                    'isProductAdded' => true
                ]);
            }

            return $this->json([
                'isProductAdded' => false,
                'message' => 'Impossible d\'ajouter le produit aux favoris',
            ]);
        }

        return $this->json([
            'message' => 'Aucun code renseigné.',
        ], 400);
    }
}
