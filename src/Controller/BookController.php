<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    // Route pour ajouter un livre
    #[Route('/book/add', name: 'book_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): JsonResponse {
        // Récupérer les données envoyées par la requête AJAX
        $data = json_decode($request->getContent(), true);

        $book = new Book();
        
        // Si les données sont envoyées en JSON, tu n'as pas besoin de traiter un formulaire Symfony classique.
        // On suppose ici que les données incluent les informations du livre
        if (isset($data['name']) && isset($data['description'])) {
            $book->setName($data['name']);
            $book->setDescription($data['description']);
            
            // Optionnel : Ajoute l'utilisateur (si besoin)
            if (isset($data['user_id'])) {
                $user = $userRepository->find($data['user_id']);
                if ($user) {
                    $book->setUser($user);
                }
            }

            $em->persist($book);
            $em->flush();

            return new JsonResponse(['status' => 'success', 'message' => 'Livre ajouté avec succès.']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Données manquantes.'], 400);
    }


    // Route pour mettre à jour la description d'un livre existant
    #[Route('/book/update-description', name: 'handle_description', methods: ['POST'])]
    public function updateDescription(
        Request $request,
        BookRepository $bookRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        // Récupérer les données envoyées par la requête AJAX
        $data = json_decode($request->getContent(), true);

        // Validation des champs nécessaires
        if (!isset($data['book_id']) || !isset($data['description'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'L\'ID du livre et la description sont requis.',
            ], 400);
        }

        // Récupérer le livre depuis la base de données
        $book = $bookRepository->find($data['book_id']);

        if (!$book) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Livre non trouvé.',
            ], 404);
        }

        // Mise à jour de la description
        $book->setDescription($data['description']);
        $em->persist($book);
        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Description mise à jour avec succès.',
        ]);
    }

}
