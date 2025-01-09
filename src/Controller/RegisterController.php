<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegisterController extends AbstractController
{

    #[Route('/register', name: 'auth.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données de mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $plainPasswordConfirm = $form->get('plainPasswordConfirm')->getData();
    
            // Vérification que les mots de passe sont identiques
            if ($plainPassword !== $plainPasswordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->render('auth/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
    
            // Hachage du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
    
            // Attribution du rôle utilisateur par défaut
            $user->setRoles(['ROLE_USER']);
    
            // Enregistrement de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Message flash pour informer l'utilisateur
            $this->addFlash('success', 'Inscription réussie !');
    
            // Redirection vers la page de connexion
            return $this->redirectToRoute('auth.login');
        }
    
        // Affichage du formulaire
        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }      
}
