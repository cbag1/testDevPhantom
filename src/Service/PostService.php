<?php

namespace App\Service;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;


class PostService
{



    private $entityManager;
    private $mailService;

    private $formFactory;


    public function __construct(EntityManagerInterface $entityManager, MailService $mailService, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->mailService = $mailService;
        $this->formFactory = $formFactory;
    }

    public function createPost($request)
    {
        $contenu = $this->getContent($request);

        $post = $this->newPost();
        $apiform = $this->createForm($post);

        $apiform->submit($contenu);

        if ($this->checkSubmitForm($apiform, $contenu)) {
            $this->saveData($post);
            return new JsonResponse(['message' => 'Post créé avec succès'], Response::HTTP_CREATED);
        }

        $errors = $this->getFormErrors($apiform);

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    private function checkSubmitForm($form, $contenu)
    {
        return $form->isSubmitted() && $form->isValid();
    }

    private function getContent($request)
    {
        return json_decode($request->getContent(), true);
    }

    private function newPost()
    {
        return new Post();
    }

    private function createForm($post)
    {
        $form = $this->formFactory->create(PostType::class,  $post, [
            'csrf_protection' => false,
        ]);

        return $form;
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
