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
        if ($request->isMethod('POST')) { 
            
            // Récupération des données du formulaire
            $prenom = $request->request->get('prenom');
            $gsm = $request->request->get('gsm');
            $email = $request->request->get('email');
            $adresse = $request->request->get('adresse');
            $ville = $request->request->get('ville');
            $pays = $request->request->get('pays');
            $password = $request->request->get('password');

            // Vérification de sécurité de base : aucun champ vide
            if (empty($prenom) || empty($gsm) || empty($email) || empty($adresse) || empty($ville) || empty($pays) || empty($password)) {
                $this->addFlash('Attention', 'Tous les champs ne sont pas remplis.');
                return $this->redirectToRoute('app_register');
            }

            // Vérification de la force du mot de passe
            $isLengthValid = strlen($password) >= 10;
            $hasUppercase = preg_match('/[A-Z]/', $password);
            $hasLowercase = preg_match('/[a-z]/', $password);
            $hasDigit = preg_match('/\d/', $password);
            $hasSpecialChar = preg_match('/[^a-zA-Z0-9]/', $password);

            if (!$isLengthValid || !$hasUppercase || !$hasLowercase || !$hasDigit || !$hasSpecialChar) {
                $this->addFlash('Attention', 'Le mot de passe ne respecte pas tous les critères de sécurité.');
                return $this->redirectToRoute('app_register');
            }

            $utilisateurExistant = $utilisateur->getUtilisateurByEmail($email);
            if ($utilisateurExistant !== null) {
                $this->addFlash('Attention', 'Cette adresse e-mail est déjà associée à un compte.');
                return $this->redirectToRoute('app_register');
            }

            // Hachage du mot de passe
            $HashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Envoi des données à la base de données via le DAO
            try {
                // On appelle la fonction du DAO pour insérer l'utilisateur
                $utilisateur->ajouterUtilisateur($prenom, $gsm, $email, $adresse, $ville, $pays, $HashedPassword);

                // TODO: Envoi du mail de bienvenue ici si besoin
                
                $this->addFlash('Bravo', 'Votre compte a bien été créé !');
                return $this->redirectToRoute('app_home'); 

            } catch (\Exception $e) {
                $this->addFlash('Attention', "Erreur de transmission à la base de données : " . $e->getMessage());
                return $this->redirectToRoute('app_register');
            }
        }
        return $this->render('register/index.html.twig');
    } 
}