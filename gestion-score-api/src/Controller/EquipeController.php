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
    //Méthode permettant d'ajouter une équipe dans la BDD
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

    //Méthode permettant de supprimer une équipe dans la BDD
    #[Route('/api/equipes/{id}', methods: ['DELETE'])]
    public function deleteEquipe(Equipe $equipe, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$equipe) {
            return $this->json(['message' => 'Équipe non trouvée'], 404);
        }

        try {
            $entityManager->remove(object: $equipe);
            $entityManager->flush();

            return $this->json(['message' => 'Équipe supprimée avec succès'], 200);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de la suppression de l\'équipe'], 500);
        }
    }

    //Méthode permettant de lister les équipes présentes en BDD
    #[Route('/api/equipes', name: 'equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, SerializerInterface $serializer): JsonResponse
    {
        $equipes = $equipeRepository->findAll();

        $jsonContent = $serializer->serialize($equipes, 'json', [
            AbstractNormalizer::GROUPS                     => ['equipe:read','joueur:read'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    //Méthode permettant de récuperer les informations d'une équipe selon son ID
    #[Route('/api/equipes/{id}', name: 'equipe_byId', methods: ['GET'])]
    public function getById(Equipe $equipe, SerializerInterface $serializer): JsonResponse
    {
        $jsonContent = $serializer->serialize($equipe, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['equipe:read','joueur:read'],
        ]);

        return new JsonResponse($jsonContent, 200, [], true);
    }

    //Méthode permettant la mise à jour d'éléments relatifs à une équipe présente en BDD
    #[Route('/api/equipes/{id}', name: 'equipe_update', methods: ['PUT'])]
    public function update(Request $request, Equipe $equipe, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $updatedEquipe = $serializer->deserialize(
            $request->getContent(),
            Equipe::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $equipe]
        );

        $entityManager->flush();

        $jsonContent = $serializer->serialize($updatedEquipe, 'json', [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::GROUPS => ['equipe:read'],
        ]);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }
}
