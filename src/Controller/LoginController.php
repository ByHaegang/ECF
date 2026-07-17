<?php

namespace App\Controller;

use App\DAO\UtilisateurDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, UtilisateurDAO $utilisateur): Response
    { 
        if ($request->isMethod('POST')) { 
            
            // Récupération des données du formulaire
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            // Vérification de sécurité de base : aucun champ vide
            if (empty($email) || empty($password)) {
                $this->addFlash('Attention', 'Tous les champs ne sont pas remplis.');
                return $this->redirectToRoute('app_login');
            }

            $utilisateurExistant = $utilisateur->getUtilisateurByEmail($email);
            if ($utilisateurExistant === null) {
                $this->addFlash('Cette adresse e-mail n\'est pas associée à un compte créé.');
                return $this->redirectToRoute('app_login');
            }

            // On appelle la fonction du DAO pour connecter l'utilisateur
            $hashedpassword = $utilisateur->getpassword($email);
            if (password_verify($password, $hashedpassword)) {
                // On récupère la session depuis la requête
                $session = $request->getSession();

                // On stocke les informations de l'utilisateur (son ID et son Email)
                $session->set('user_email', $utilisateurExistant['email']);
                $session->set('user_prenom', $utilisateurExistant['prenom']);;
                $this->addFlash('Bravo','Connextion Réussie');
                return $this->redirectToRoute('app_home'); 
            } else {
                $this->addFlash('Attention', "Erreur de connexion à la base de données");
                return $this->redirectToRoute('app_login');
            }

            // TODO: Envoi du mail de réinitialisation du mot de passe si besoin

        }
        return $this->render('login/index.html.twig');
    } 
}