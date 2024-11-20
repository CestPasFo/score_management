<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Score;
use App\Repository\ScoreRepository;
use App\Repository\EquipeRepository;

class ScoreController extends AbstractController
{
    #[Route('/api/scores', name: 'score_index', methods: ['GET'])]
    public function index(ScoreRepository $scoreRepository, SerializerInterface $serializer): JsonResponse
    {
        $scores = $scoreRepository->findAll();

        $jsonContent = $serializer->serialize($scores, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS                     => ['score:read','equipe:read','joueur:read'],
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    // #[Route('/api/scores', name: 'score_index', methods: ['GET'])]
    // public function index(ScoreRepository $scoreRepository): JsonResponse
    // {
    //     $scores = $scoreRepository->findAll();

    //     return $this->json([
    //         'match' => array_map(callback: function($scores): array {
    //             return [
    //                 'id' => $scores->getId(),
    //                 'Equipe A' => $scores->getEquipeA(),
    //                 'score' => $scores->getScore(),
    //                 'Equipe B' => $scores->getEquipeB(),
    //             ];
    //         }, array: $scores)
    //     ]);
    // }

    #[Route('/api/scores/{id}', name: 'score_byId', methods: ['GET'])]
    public function getById(Score $score): JsonResponse
    {
        return $this->json([
            'id' => $score->getId(),
            'Equipe A' => $score->getEquipeA(),
            'score' => $score->getScore(),
            'Equipe B' => $score->getEquipeB(),
        ]);
    } 
    
    #[Route('/api/scores/{id}', methods: ['DELETE'])]
    public function deleteEquipe(Score $score, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$score) {
            return $this->json(['message' => 'match non trouvé'], 404);
        }

        try {
            $entityManager->remove(object: $score);
            $entityManager->flush();
            return $this->json(['message' => 'match supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la suppression du match'], 500);
        }
    }

    #[Route('/api/scores', name: 'score_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, EquipeRepository $equipeRepository): JsonResponse
    {
        $data = json_decode(json: $request->getContent(), associative: true);

        $equipeA = $equipeRepository->find($data['equipeA_id']);
        $equipeB = $equipeRepository->find($data['equipeB_id']);

        if (!$equipeA || !$equipeB) {
            return $this->json(['error' => 'Une ou les deux équipes n\'existent pas'], 404);
        }

        // Créer un nouveau score
        $score = new Score();
        $score->setEquipeA(equipeA: $equipeA);
        $score->setEquipeB(equipeB: $equipeB);
        $score->setScore(score: $data['score']);

        // Persister le score
        $entityManager->persist(object: $score);
        $entityManager->flush();

        // Retourner la réponse
        return $this->json([
            'message' => 'Match créé avec succès',
            'id' => $score->getId(),
            'equipeA' => $score->getEquipeA()->getNom(),
            'equipeB' => $score->getEquipeB()->getNom(),  
            'score' => $score->getScore()
        ], 201);
    }
}
