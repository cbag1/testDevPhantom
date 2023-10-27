<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('api/register', name: 'app_user_registration', methods: ['POST'])]
    public function registration(UserPasswordHasherInterface $pwdHasher, Request $request): JsonResponse
    {
        $user = new User();
        $content = json_decode($request->getContent());
      
        $email = $content->email;
        $plaintextPassword = $content->password;
    
        $hashedPassword = $pwdHasher->hashPassword(
            $user,
            $plaintextPassword
        );
    
        $user->setPassword($hashedPassword);
        $user->setEmail($email);

        $this->entityManager->persist($user);
        $this->entityManager->flush();


        return new JsonResponse(['message' => 'User ajouté avec succés'], Response::HTTP_CREATED);
    }

}
