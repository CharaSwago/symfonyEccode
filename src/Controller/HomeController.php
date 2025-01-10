<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private BookReadRepository $readBookRepository;

    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->readBookRepository = $bookReadRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(BookRepository $bookRepository): Response
    {

        if (!$this->getUser()) {

            return $this->redirectToRoute('auth.login');
        }


        $userId = $this->getUser()->getId();  
        $booksRead = $this->readBookRepository->findByUserId($userId, false);  


        $books = $bookRepository->findAll();


        $selectedRating = 4; 


        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'books' => $books,
            'name' => 'Accueil',
            'selected_rating' => $selectedRating,  
        ]);
    }

    #[Route('/login', name: 'auth.login')]
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app.home');
        }

        return $this->render('auth/login.html.twig', [
            'name' => 'Thibaud', 
        ]);
    }

    #[Route('/register', name: 'auth.register')]
    public function register(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app.home');
        }

        return $this->render('auth/register.html.twig', [
            'name' => 'Thibaud', 
        ]);
    }

}
