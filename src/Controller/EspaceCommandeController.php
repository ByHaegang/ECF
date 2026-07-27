<?php

namespace App\Controller;

use App\DAO\MenuDAO;
use App\DAO\UtilisateurDAO;
use App\DAO\CommandeDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EspaceCommandeController extends AbstractController
{
    #[Route('/EspaceCommande', name: 'app_espacecommande')]
    public function index(Request $request, CommandeDAO $commande, MenuDAO $menuDAO, UtilisateurDAO $user, MailerInterface $mailer): Response
    {

        $menuIdSelectionne = $request->query->get('menu_id');
        $menus = $menuDAO->getAllMenus();

        if ($request->isMethod('POST')) 
            {
            
            if (!$this->isCsrfTokenValid('espaceCommande-form', $request->request->get('_token'))) {
                $this->addFlash('attention', 'Requête invalide ou session expirée (Erreur CSRF).');
                return $this->redirectToRoute('app_espacecommande');
            }


            // Récupération informations de la commande
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $prenom = $request->request->get('prenom');
            $adressePrestation = $request->request->get('villePrestation');
            $gsm = $request->request->get('gsm');
            $datePrestation = $request->request->get('datePrestation');
            $heurePrestation = $request->request->get('heurePrestation');
            $menuId = $request->request->get('menu');
            $nombrePersonne = $request->request->get('nombrePersonne');

            if ( $menuId === 'Tous les menus') {
                $this->addFlash('attention', 'Veuillez sélectionner un menu.');

                return $this->render('espaceCommande/index.html.twig', [
                    'menus' => $menus,
                    'menu_id_selectionne' => $menuIdSelectionne
                ]);
            };

            if (empty($name) || empty($email) || empty($prenom) || empty($adressePrestation) || empty($heurePrestation) || empty($datePrestation) || empty($gsm) || empty($menuId) || empty($nombrePersonne)) {
                
                $this->addFlash('attention', 'Tous les champs ne sont pas remplis.');
                
                return $this->render('espaceCommande/index.html.twig', [
                    'menus' => $menus,
                    'menu_id_selectionne' => $menuIdSelectionne
                ]);
            }

            // Si tous les champs sont remplis, on continue le traitement
            $prixUnitaireCentimes = $menuDAO->getMenuPriceById($menuId);
            $prixTotalCentimes = $prixUnitaireCentimes * $nombrePersonne;

            $userData = $user->getUtilisateurByEmail($email);
            
            $userId = $userData ? $userData['utilisateur_id'] : null;

            if ($userId === null) {
                $this->addFlash('attention', 'Utilisateur non existant. Veuillez vous inscrire ou vérifier votre email.');
                return $this->render('espaceCommande/index.html.twig', [
                    'menus' => $menus,
                    'menu_id_selectionne' => $menuIdSelectionne
                ]);
            }

            // Insertion de la commande
            $commande->ajouterCommande($userId, $menuId, $nombrePersonne, $datePrestation, $heurePrestation, $prixTotalCentimes);
            
            $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');

            // On re-rend la page du formulaire avec les menus
            return $this->render('espaceCommande/index.html.twig', [
                'menus' => $menus,
                'menu_id_selectionne' => null // On réinitialise la sélection après une commande réussie
            ]);
        }

        // Affichage de la page par défaut
        return $this->render('espaceCommande/index.html.twig', [
            'menus' => $menus,
            'menu_id_selectionne' => $menuIdSelectionne
        ]);
    }
}