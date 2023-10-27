<?php

namespace App\Service;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;



class PostService
{



    private $entityManager;
    private $mailService;

    public function __construct(EntityManagerInterface $entityManager, MailService $mailService)
    {
        $this->entityManager = $entityManager;
        $this->mailService = $mailService;
    }


    public function getAllPosts()
    {
        $posts = $this->entityManager->getRepository(Post::class)->findBy(['archived' => 0]);
        return $posts;
    }

    public function saveData(Post $post)
    {
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function updatePost(Post $post, array $data)
    {
        $post->setTitre($data['titre']);
        $post->setContent($data['content']);
    
        $this->saveData($post);
    }


    public function processValidPost(Post $post, array $contenu): void
    {
        $this->saveData($post);
        $this->mailService->sendEmail($contenu['titre']);
    }

    public function getFormErrors($form)
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }
        return $errors;
    }

    public function deletePost($post)
    {
        $post->setArchived(true);
        $this->saveData($post);
    }
}
