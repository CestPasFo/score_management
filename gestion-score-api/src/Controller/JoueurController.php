<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Joueur;
use App\Repository\JoueurRepository;

class JoueurController extends AbstractController
{
    #[Route('/api/joueurs', methods: ['POST'])]
    public function addJoueur(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode(json: $request->getContent(), associative: true);

        $joueur = new Joueur();
        $joueur->setName(name: $data['nom'] ?? '');
        $joueur->setFirstname(firstname: $data[''] ??'');
 
        $entityManager->persist(object: $joueur);
        $entityManager->flush();
        return $this->json([
             'message' => 'Équipe créée avec succès',
             'id' => $joueur->getId(),
             'nom' => $joueur->getName(),
             'prenom' => $joueur->getFirstname()
         ], 201);

    }

    #[Route('/api/joueurs/{id}', methods: ['DELETE'])]
    public function deleteEquipe(Joueur $joueur, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifier si l'équipe existe
        if (!$joueur) {
            return $this->json(['message' => 'Joueur non trouvé'], 404);
        }

        try {
            // Supprimer l'équipe
            $entityManager->remove(object: $joueur);
            $entityManager->flush();

            return $this->json(['message' => 'Joueur supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la suppression du joueur'], 500);
        }
    }

    #[Route('/api/joueurs', name: 'joueur_index', methods: ['GET'])]
    public function index(JoueurRepository $joueurRepository): JsonResponse
    {
        $joueurs = $joueurRepository->findAll();

        return $this->json([
            'joueurs' => array_map(callback: function($joueur): array {
                return [
                    'id' => $joueur->getId(),
                    'prenom' => $joueur->getFirstname(),
                    'nom' => $joueur->getName(),
                ];
            }, array: $joueurs)
        ]);
    }

    #[Route('/api/joueurs/{id}', name: 'joueurById', methods: ['GET'])]
    public function getById(Joueur $joueur): JsonResponse
    {
        return $this->json([
            'id' => $joueur->getId(),
            'prenom' => $joueur->getFirstname(),
            'nom' => $joueur->getName(),
        ]);
    }
}
