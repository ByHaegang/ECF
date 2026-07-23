<?php

namespace App\Controller;

use App\DAO\UtilisateurDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UtilisateurDAO $utilisateur, MailerInterface $mailer): Response
    { 
        if ($request->isMethod('POST')) 
            {
            
            if (!$this->isCsrfTokenValid('register-form', $request->request->get('_token'))) {
                $this->addFlash('attention', 'Requête invalide ou session expirée (Erreur CSRF).');
                return $this->redirectToRoute('app_register');
            }

            // Récupération des données du formulaire
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $gsm = $request->request->get('gsm');
            $email = $request->request->get('email');
            $adresse = $request->request->get('adresse');
            $ville = $request->request->get('ville');
            $pays = $request->request->get('pays');
            $password = $request->request->get('password');

            // Vérification de sécurité de base : aucun champ vide
            if (empty($nom) || empty($prenom) || empty($gsm) || empty($email) || empty($adresse) || empty($ville) || empty($pays) || empty($password)) {
                $this->addFlash('attention', 'Tous les champs ne sont pas remplis.');
                return $this->redirectToRoute('app_register');
            }

            // Vérification de la force du mot de passe
            $isLengthValid = strlen($password) >= 10;
            $hasUppercase = preg_match('/[A-Z]/', $password);
            $hasLowercase = preg_match('/[a-z]/', $password);
            $hasDigit = preg_match('/\d/', $password);
            $hasSpecialChar = preg_match('/[^a-zA-Z0-9]/', $password);

            if (!$isLengthValid || !$hasUppercase || !$hasLowercase || !$hasDigit || !$hasSpecialChar) {
                $this->addFlash('attention', 'Le mot de passe ne respecte pas tous les critères de sécurité.');
                return $this->redirectToRoute('app_register');
            }

            $utilisateurExistant = $utilisateur->getUtilisateurByEmail($email);
            if ($utilisateurExistant !== null) {
                $this->addFlash('attention', 'Cette adresse e-mail est déjà associée à un compte.');
                return $this->redirectToRoute('app_register');
            }

            // Hachage du mot de passe
            $HashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Envoi des données à la base de données via le DAO
            try {
                // On appelle la fonction du DAO pour insérer l'utilisateur
                $utilisateur->ajouterUtilisateur($nom, $prenom, $gsm, $email, $adresse, $ville, $pays, $HashedPassword);

                // TODO: Envoi du mail de bienvenue ici si besoin
                
                $this->addFlash('success', 'Votre compte a bien été créé !');
                return $this->redirectToRoute('app_login'); 

            } catch (\Exception $e) {
                $this->addFlash('attention', "Erreur de transmission à la base de données : " . $e->getMessage());
                return $this->redirectToRoute('app_register');
            }
        }
        return $this->render('register/index.html.twig');
    } 
}