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
            $products = $this->productSrvc->fetchProductsApi($request->get('searchTerms'), $this->getUser());

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

    #[Route('/delete/{codeEan}', name: 'delete_favorite', methods: ['DELETE'])]
    public function deleteProductFavorite(Request $request)
    {
        if ($request->get('codeEan') && $request->get('codeEan') !== '') {

            $saveProduct = $this->productSrvc->removeProductUserFavorites($request->get('codeEan'), $this->getUser());

            if ($saveProduct) {
                return $this->json([
                    'isProductRemoved' => true
                ]);
            }

            return $this->json([
                'isProductRemoved' => false,
                'message' => 'Impossible de retirer le produit des favoris.',
            ]);
        }

        return $this->json([
            'message' => 'Aucun code renseigné.',
        ], 400);
    }

    #[Route('/clear', name: 'clear_favorites', methods: ['GET'])]
    public function clearProductFavorites()
    {
        $this->productSrvc->clearProductUserFavorites($this->getUser());

        return $this->json([
            'isClearedFavorites' => true,
        ]);
    }

    #[Route('/exclude/{codeEan}', name: 'exclude_product', methods: ['GET'])]
    public function excludeProduct(Request $request)
    {
        if ($request->get('codeEan') && $request->get('codeEan') !== '') {

            $excludeProduct = $this->productSrvc->excludeProductUser($request->get('codeEan'), $this->getUser());

            if ($excludeProduct) {
                return $this->json([
                    'isProductExcluded' => true
                ]);
            }

            return $this->json([
                'isProductExcluded' => false,
                'message' => 'Impossible d\'exclure le produit.',
            ]);
        }

        return $this->json([
            'message' => 'Aucun code renseigné.',
        ], 400);
    }
}
