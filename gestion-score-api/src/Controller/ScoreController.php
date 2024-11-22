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
use Symfony\Component\Security\Http\Attribute\IsGranted;


class ScoreController extends AbstractController
{
    //Méthode permettant le listing des matchs présents dans la BDD
    #[Route('/api/scores', name: 'score_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(ScoreRepository $scoreRepository, SerializerInterface $serializer): JsonResponse
    {
        $scores = $scoreRepository->findAll();

        $jsonContent = $serializer->serialize($scores, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['score:read','equipe:read','joueur:read'],
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    //Méthode permettant le listing des matchs présents dans la BDD en fonction d'un ID    
    #[Route('/api/scores/{id}', name: 'score_byId', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getById(Score $score, SerializerInterface $serializer): JsonResponse
    {
        $jsonContent = $serializer->serialize($score, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS                     => ['score:read','equipe:read','joueur:read'],
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }
    
    //Méthode permettant la suppression d'un match présents dans la BDD
    #[Route('/api/scores/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteScore(Score $score, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($score);
            $entityManager->flush();
            return $this->json(['message' => 'Match supprimé avec succès'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['message' => $e.'Erreur lors de la suppression du match'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //Méthode permettant l'ajout d'un match dans la BDD
    #[Route('/api/scores', name: 'score_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $entityManager, EquipeRepository $equipeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['equipeA_id'], $data['equipeB_id'], $data['score'])) {
            return $this->json(['error' => 'Données incomplètes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $equipeA = $equipeRepository->find($data['equipeA_id']);
        $equipeB = $equipeRepository->find($data['equipeB_id']);

        if (!$equipeA || !$equipeB) {
            return $this->json(['error' => 'Une ou les deux équipes n\'existent pas'], JsonResponse::HTTP_NOT_FOUND);
        }

        $score = new Score();
        $score->setEquipeA($equipeA);
        $score->setEquipeB($equipeB);
        $score->setScore($data['score']);

        $entityManager->persist($score);
        $entityManager->flush();

        return $this->json([
            'message' => 'Match créé avec succès',
            'id' => $score->getId(),
            'equipeA' => $score->getEquipeA()->getId(),
            'equipeB' => $score->getEquipeB()->getId(),  
            'score' => $score->getScore()
        ], JsonResponse::HTTP_CREATED);
    }

    //Méthode permettant de mettre à jour les informations relatifs à un match présentes en BDD
    #[Route('/api/scores/{id}', name: 'score_update', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, Score $score, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $updatedScore = $serializer->deserialize(
            $request->getContent(),
            Score::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $score]
        );

        $entityManager->flush();

        $jsonContent = $serializer->serialize($updatedScore, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['score:read','equipe:read'],
        ]);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }
}
