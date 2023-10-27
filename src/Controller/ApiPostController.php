<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\MailService;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiPostController extends AbstractController
{

    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    #[Route('/api/posts', name: 'app_api_post', methods: ['GET'])]
    public function index(PostRepository $postRepository)
    {
        $posts = $this->postService->getAllPosts();
        return  $this->json($posts, 200, [], ['groups' => 'post:read']);
    }


    #[Route('/api/posts', name: 'app_api_post_new', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {

        return $this->postService->createPost($request);
        // $contenu = json_decode($request->getContent(), true);

        // $post = new Post();

        // $apiform = $this->createForm(PostType::class, $post, [
        //     'csrf_protection' => false,
        // ]);

        // $apiform->submit($contenu);

        // if ($apiform->isSubmitted() && $apiform->isValid()) {

        //     $this->postService->processValidPost($post, $contenu);

        //     return new JsonResponse(['message' => 'Post créé avec succès'], Response::HTTP_CREATED);
        // }

        // $errors = $this->postService->getFormErrors($apiform);

        // return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }



    #[Route('/api/posts/{id}', name: 'app_api_post_show')]
    public function show(PostRepository $postRepository, int $id)
    {
        $post = $postRepository->find($id);
        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($post, 200, [], ['groups' => 'post:read']);
    }


    #[Route('/api/posts/{id}/edit', name: 'app_api_post_edit', methods: ['PUT'])]
    public function edit(PostRepository $postRepository, int $id, Request $request): JsonResponse
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $contenu = json_decode($request->getContent(), true);

        $this->postService->updatePost($post, $contenu);

        return new JsonResponse(['message' => 'User modifié avec succés']);
    }

    #[Route('/api/posts/{id}/delete', name: 'app_api_post_delete')]
    public function delete(PostRepository $postRepository, int $id): JsonResponse
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->postService->deletePost($post);


        return new JsonResponse(['message' => 'Post supprimé avec succés']);
    }
}
