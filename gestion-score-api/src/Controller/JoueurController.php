<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Joueur;
use App\Repository\JoueurRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Repository\EquipeRepository;

class JoueurController extends AbstractController
{
    //Méthode permettant l'ajout d'un joueur dans la BDD
    #[Route('/api/joueurs', methods: ['POST'])]
    public function addJoueur(Request $request, EntityManagerInterface $entityManager, EquipeRepository $equipeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifier si l'équipe existe
        $equipe = $equipeRepository->find((int)($data['equipeId'] ?? 0));
        if (!$equipe) {
            return $this->json([
                'message' => 'Équipe non trouvée'
            ], 404);
        }
        $joueur = new Joueur();
        $joueur->setEquipe($equipe);
        $joueur->setName($data['nom'] ?? '');
        $joueur->setFirstname($data['firstname'] ?? '');

        $entityManager->persist($joueur);
        $entityManager->flush();

        return $this->json([
            'message' => 'Le joueur a été créé avec succès',
            'id' => $joueur->getId(),
            'equipeId' => $joueur->getEquipe()->getId(),
            'nom' => $joueur->getName(),
            'prenom' => $joueur->getFirstname()
        ], 201);
    }

    //Méthode permettant de supprimer un joueur de la BDD
    #[Route('/api/joueurs/{id}', methods: ['DELETE'])]
    public function deleteJoueur(Joueur $joueur, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $entityManager->remove($joueur);
            $entityManager->flush();

            return $this->json(['message' => 'Joueur supprimé avec succès'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la suppression du joueur'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //Méthode permettant de lister les joueurs
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

    //Méthode permettant de lister les joueurs selon leurs ID
    #[Route('/api/joueurs/{id}', name: 'joueurById', methods: ['GET'])]
    public function getById(Joueur $joueur): JsonResponse
    {
        return $this->json([
            'id' => $joueur->getId(),
            'prenom' => $joueur->getFirstname(),
            'nom' => $joueur->getName(),
        ]);
    }

    //Méthode permettant de mettre à jour les informations relatifs à un joueur en BDD
    #[Route('/api/joueurs/{id}', name: 'joueur_update', methods: ['PUT'])]
    public function update(Request $request, Joueur $joueur, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $updatedJoueur = $serializer->deserialize(
            $request->getContent(),
            Joueur::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $joueur]
        );

        $entityManager->flush();

        $jsonContent = $serializer->serialize($updatedJoueur, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['joueur:read'],
        ]);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }

}
