<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;
use OpenApi\Attributes as OA;

#[Route('/api')]
class EventController extends BaseController
{
    public function __construct(
        private EventRepository $eventRepository
    )
    {}

    #[Route('/events', name: 'app_events', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Event::class))
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
    #[OA\Parameter(
        name: 'type',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = $request->query->all() ?? [];
            $orderBy = ['date' => 'DESC'];

            $events = $this->eventRepository->findByAndFilter($criteria, $orderBy);

            return $this->json($events, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage());
        }
    }

    #[Route('/event', name: 'app_event_create', methods: ['POST'])]
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
            content: new OA\JsonContent(ref: new Model(type: Event::class))
        )
    )]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $event = new Event();
            $this->setProperties($event, $request->getPayload());

            $em->persist($event);
            $em->flush();

            return $this->json("L'élément a été créé avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/event/{id}', name: 'app_event_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Event::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    public function show(Event $event): JsonResponse
    {
        try {
            return $this->json($event, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/event/{id}', name: 'app_event_update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
    )]
    #[OA\Response(
        response: 500,
        description: 'Server error',
    )]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: Event::class))
        )
    )]
    public function update(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        try {
            $event = $this->eventRepository->find($id);

            $this->setProperties($event, $request->getPayload());

            $em->flush();

            return $this->json("L'élément a été modifié avec succès", 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/event/{id}', name: 'app_event_delete', methods: ['DELETE'])]
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
            $event = $this->eventRepository->find($id);
            $em->remove($event);

            $em->flush();

            return $this->json("L'élément a été supprimé avec succès");
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }
}
