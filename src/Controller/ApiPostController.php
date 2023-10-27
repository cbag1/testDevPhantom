<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiPostController extends AbstractController
{

    private $entityManager;
    private $mailService;

    public function __construct(EntityManagerInterface $entityManager, MailService $mailService)
    {
        $this->mailService = $mailService;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/posts', name: 'app_api_post', methods: ['GET'])]
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findBy(['archived' => 0]);
        return $this->json($posts, 200, [], ['groups' => 'post:read']);
    }

    #[Route('/api/posts', name: 'app_api_post_new', methods: ['POST'])]
    public function new(PostRepository $postRepository, NormalizerInterface $normalizer, Request $request): JsonResponse
    {
        $contenu = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setTitre($contenu['titre']);
        $post->setContent($contenu['content']);
        $post->setArchived(0);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        #mail
        $val = $this->mailService->sendEmail($contenu['titre']);
        // dd($val);

        return new JsonResponse(['message' => 'Post ajouté avec succés'], Response::HTTP_CREATED);
    }

    #[Route('/api/posts/{id}', name: 'app_api_post_show')]
    public function show(PostRepository $postRepository, NormalizerInterface $normalizer, int $id)
    {
        $post = $postRepository->find($id);
        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($post, 200, [], ['groups' => 'post:read']);
        // $post = $postRepository->find($id);

        // return new Response($normalizer->normalize($post));
    }

    #[Route('/api/posts/{id}/edit', name: 'app_api_post_edit', methods: ['PUT'])]
    public function edit(PostRepository $postRepository, NormalizerInterface $normalizer, int $id, Request $request): JsonResponse
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $contenu = json_decode($request->getContent(), true);
        // dd($contenu);
        $post->setTitre($contenu['titre']);
        $post->setContent($contenu['content']);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User modifié avec succés']);
    }

    #[Route('/api/posts/{id}/delete', name: 'app_api_post_delete')]
    public function delete(PostRepository $postRepository, NormalizerInterface $normalizer, int $id): JsonResponse
    {
        $post = $postRepository->find($id);

        if (!$post) {
            return new JsonResponse(['message' => 'Post non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $post->setArchived(1);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Post supprimé avec succés']);
    }
}
