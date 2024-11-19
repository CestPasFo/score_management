<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class EquipeController extends AbstractController
{

    #[Route('', methods: ['GET'])]
    public function index(EquipeRepository $teamRepository): JsonResponse
    {
        $teams = $teamRepository->findAllWithPlayersAndScores();
        return $this->json($teams, 200, [], ['groups' => 'team:read']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getById(Equipe $team): JsonResponse
    {
        return $this->json($team, 200, [], ['groups' => 'team:read']);
    }

    #[Route('/api/equipes', methods: ['POST'])]
    public function addEquipe(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode(json: $request->getContent(), associative: true);

        $equipe = new Equipe();
        $equipe->setNom(nom: $data['nom'] ?? '');
        $equipe->setNbdefaite(nbdefaite: 0);
        $equipe->setNbvictoire(nbvictoire: 0);
        $equipe->setNbmatch(nbmatch: 0);
 
        $entityManager->persist(object: $equipe);
        $entityManager->flush();
        return $this->json([
             'message' => 'Équipe créée avec succès',
             'id' => $equipe->getId(),
             'nom' => $equipe->getNom()
         ], 201);

    }

    #[Route('/api/equipes/{id}', methods: ['DELETE'])]
    public function deleteEquipe(Equipe $equipe, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifier si l'équipe existe
        if (!$equipe) {
            return $this->json(['message' => 'Équipe non trouvée'], 404);
        }

        try {
            // Supprimer l'équipe
            $entityManager->remove(object: $equipe);
            $entityManager->flush();

            return $this->json(['message' => 'Équipe supprimée avec succès'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la suppression de l\'équipe'], 500);
        }
    }
}
