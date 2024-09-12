<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;
use OpenApi\Attributes as OA;

#[Route('/api')]
class CommentController extends BaseController
{
    public function __construct(
        private CommentRepository $commentRepository
    )
    {
    }

    #[Route('/comments', name: 'app_comments', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Comment::class))
        ),
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    #[OA\Parameter(
        name: 'name',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'category',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = $request->query->all() ?? [];
            $orderBy = ['name' => 'DESC'];

            $comments = $this->commentRepository->findByAndFilter($criteria, $orderBy);

            return $this->json($comments, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage());
        }
    }

    #[Route('/comment', name: 'app_comment_create', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: Comment::class))
        )
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $comment = new Comment();
            $this->setProperties($comment, $request->getPayload());

            $em->persist($comment);
            $em->flush();

            return $this->json("L'élément a été créé avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/comment/{id}', name: 'app_comment_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Comment::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    public function show(Comment $comment): JsonResponse
    {
        try {
            return $this->json($comment, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/comment/{id}', name: 'app_comment_update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: Comment::class))
        )
    )]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $comment = $this->commentRepository->find($id);

            $this->setProperties($comment, $request->getPayload());

            $em->flush();

            return $this->json("L'élément a été modifié avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/comment/{id}', name: 'app_comment_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $comment = $this->commentRepository->find($id);
            $em->remove($comment);

            $em->flush();

            return $this->json("L'élément a été supprimé avec succès");
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }
}
