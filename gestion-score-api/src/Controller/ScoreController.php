<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Score;
use App\Repository\ScoreRepository;
use App\Repository\EquipeRepository;

class ScoreController extends AbstractController
{
    #[Route('/api/score', name: 'score_index', methods: ['GET'])]
    public function index(ScoreRepository $scoreRepository): JsonResponse
    {
        $scores = $scoreRepository->findAll();

        return $this->json([
            'equipes' => array_map(callback: function($equipeA,$equipeB, $score): array {
                return [
                    'id de l\'equipe A' => $equipeA->getId(),
                    'nom de l\'equipe A' => $equipeA->getNom(),
                    'score du match' => $score->getScore(),
                    'id de l\'equipe B'=> $equipeB->getId(),
                    'nom de l\'equipe B' => $equipeB->getNom(),
                ];
            }, array: $scores)
        ]);
    }

    #[Route('/api/score/{id}', name: 'score_byId', methods: ['GET'])]
    public function getById(Score $score): JsonResponse
    {
        return $this->json([
            'id' => $score->getId(),
            'Equipe A' => $score->getEquipeA(),
            'score' => $score->getScore(),
            'Equipe B' => $score->getEquipeB(),
        ]);
    } 
    
    #[Route('/api/score/{id}', methods: ['DELETE'])]
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
