<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api')]
class ArtistController extends BaseController
{
    public function __construct(
        private ArtistRepository $artistRepository
    )
    {}

    #[Route('/artists', name: 'app_artists', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = $request->query->all() ?? [];
            $orderBy = ['date' => 'DESC'];

            $artists = $this->artistRepository->findByAndFilter($criteria, $orderBy);

            return $this->json($artists, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage());
        }
    }

    #[Route('/artist', name: 'app_artist_create', methods: ['POST'])]
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
    public function show(Artist $artist): JsonResponse
    {
        try {
            return $this->json($artist, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/artist/{id}', name: 'app_artist_update', methods: ['PUT'])]
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
