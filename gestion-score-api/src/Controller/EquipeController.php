<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class EquipeController extends AbstractController
{
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

    // #[Route('/api/equipes', name: 'equipe_index', methods: ['GET'])]
    // public function index(EquipeRepository $equipeRepository): JsonResponse
    // {
    //     $equipes = $equipeRepository->findAll();

    //     return $this->json([
    //         'equipes' => array_map(callback: function($equipe): array {
    //             return [
    //                 'id' => $equipe->getId(),
    //                 'nom' => $equipe->getNom(),
    //                 'joueurs' => $equipe->getJoueurs(),
    //                 'nbdefaite' => $equipe->getNbdefaite(),
    //                 'nbvictoire' => $equipe->getNbvictoire(),
    //                 'nbmatch' => $equipe->getNbmatch(),
    //             ];
    //         }, array: $equipes)
    //     ]);
    // }

    #[Route('/api/equipes', name: 'equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $equipes = $equipeRepository->findAll();

        $jsonContent = $serializer->serialize($equipes, 'json', [
            AbstractNormalizer::GROUPS                     => ['equipe:read','joueur:read'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['__initializer__', '__cloner__', '__isInitialized__'],
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/api/equipes/{id}', name: 'equipe_byId', methods: ['GET'])]
    public function getById(Equipe $equipe): JsonResponse
    {
        return $this->json([
            'id' => $equipe->getId(),
            'nom' => $equipe->getNom(),
            'joueurs' => $equipe->getJoueurs(),
            'nbdefaite' => $equipe->getNbdefaite(),
            'nbvictoire' => $equipe->getNbvictoire(),
            'nbmatch' => $equipe->getNbmatch(),
        ]);
    }
}
