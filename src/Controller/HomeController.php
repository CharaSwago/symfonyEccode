<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private BookReadRepository $readBookRepository;

    // Inject the repository via the constructor
    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            // Si non, rediriger vers la page de login
            return $this->redirectToRoute('auth.login');
        }

        // Si l'utilisateur est connecté, récupérer les livres lus
        $userId     = $this->getUser()->getId();  // Assurez-vous d'utiliser l'ID de l'utilisateur connecté
        $booksRead  = $this->bookReadRepository->findByUserId($userId, false);

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'name'      => 'Accueil', // Passer des données à la vue
        ]);
    }

    #[Route('/login', name: 'auth.login')]
    public function login(): Response
    {
        // Vérifier si l'utilisateur est déjà connecté, rediriger vers l'accueil si c'est le cas
        if ($this->getUser()) {
            return $this->redirectToRoute('app.home');
        }

        // Si non connecté, afficher la page de login
        return $this->render('auth/login.html.twig', [
            'name' => 'Thibaud', // Passer des données à la vue
        ]);
    }

    #[Route('/register', name: 'auth.register')]
    public function register(): Response
    {
        // Vérifier si l'utilisateur est déjà connecté, rediriger vers l'accueil si c'est le cas
        if ($this->getUser()) {
            return $this->redirectToRoute('app.home');
        }

        // Si non connecté, afficher la page d'inscription
        return $this->render('auth/register.html.twig', [
            'name' => 'Thibaud', // Passer des données à la vue
        ]);
    }
}