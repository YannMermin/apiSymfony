<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api')]
class AuthController extends AbstractController
{
    public function __construct(private UserRepository $userRepository, private Security $security, private SerializerInterface $serializer)
    {
    }

    #[Route('/register', name: 'register')]
    public function index(Request $request)
    {
        $formData = json_decode($request->getContent());

        $createUser = $this->userRepository->create($formData);

        if ($createUser['error']) {
            return $this->json([
                'error' => $createUser['message']
            ], 400);
        }

        return $this->json([
            'user' => $this->serializer->serialize($createUser['user'], 'json')
        ]);
    }
}
