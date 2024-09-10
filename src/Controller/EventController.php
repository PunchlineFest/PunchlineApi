<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/api')]
class EventController extends BaseController
{
    public function __construct(
        private EventRepository $eventRepository
    )
    {}

    #[Route('/events', name: 'app_events', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = $request->query->all('criteria') ?? [];
            $orderBy = $request->query->all('order') ?? ['date' => 'DESC'];

            $events = $this->eventRepository->findByAndFilter($criteria, $orderBy);

            return $this->json($events, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage());
        }
    }

    #[Route('/event', name: 'app_event_create', methods: ['POST'])]
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
    public function show(Event $event): JsonResponse
    {
        try {
            return $this->json($event, 200);
        } catch (throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route('/event/{id}', name: 'app_event_update', methods: ['PUT'])]
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
