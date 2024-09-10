<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;
use OpenApi\Attributes as OA;

#[Route('/api')]
class ArtistController extends BaseController
{
    public function __construct(
        private ArtistRepository $artistRepository
    )
    {
    }

    #[Route('/artists', name: 'app_artists', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Artist::class))
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

            $artists = $this->artistRepository->findByAndFilter($criteria, $orderBy);

            return $this->json($artists, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage());
        }
    }

    #[Route('/artist', name: 'app_artist_create', methods: ['POST'])]
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
            content: new OA\JsonContent(ref: new Model(type: Artist::class))
        )
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $artist = new Artist();
            $this->setProperties($artist, $request->getPayload());

            $em->persist($artist);
            $em->flush();

            return $this->json("L'élément a été créé avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/artist/{id}', name: 'app_artist_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Artist::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    public function show(Artist $artist): JsonResponse
    {
        try {
            return $this->json($artist, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/artist/{id}', name: 'app_artist_update', methods: ['PUT'])]
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
            content: new OA\JsonContent(ref: new Model(type: Artist::class))
        )
    )]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $artist = $this->artistRepository->find($id);

            $this->setProperties($artist, $request->getPayload());

            $em->flush();

            return $this->json("L'élément a été modifié avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/artist/{id}', name: 'app_artist_delete', methods: ['DELETE'])]
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
            $artist = $this->artistRepository->find($id);
            $em->remove($artist);

            $em->flush();

            return $this->json("L'élément a été supprimé avec succès");
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }
}
