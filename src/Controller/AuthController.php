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
    private $userRepository;
    private $security;
    private $serializer;

    public function __construct(UserRepository $userRepository, Security $security, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
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
