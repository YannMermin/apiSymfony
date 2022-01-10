<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiProductController extends AbstractController
{
    /**
     * @Route("/api/product", name="api_product_index", methods={"GET"})
     */
    #[Route('/product', name: 'product')]
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ], 200);
    }
}
