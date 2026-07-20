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

        if ($request->isMethod('Post')) {

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

            if (empty($name) || empty($email) || empty($prenom) || empty($adressePrestation) || empty($heurePrestation) || empty($datePrestation) || empty($gsm) || empty($menu) || empty($nombrePersonne)) {
            $this->addFlash('Attention', 'Tous les champs ne sont pas remplis.');

            $prixUnitaireCentimes = $menuDAO->getMenuPriceById($menuId);
            $prixTotalCentimes = $prixUnitaireCentimes * $nombrePersonne;

            $userData = $user->getUtilisateurByEmail($email);
            $userId = $userData ? $userData['id'] : null;

            if ($userId === null) {
            $this->addFlash('Attention', 'Utilisateur non existant. Veuillez vous inscrire ou vérifier votre email.');
            return $this->render('espaceCommande/index.html.twig', [
                'menus' => $menus
            ]);
        }

            $commande->ajouterCommande($userId, $menuId, $nombrePersonne, $datePrestation, $heurePrestation, $prixTotalCentimes);
            
            // 2. On RE-REND la page du formulaire directement
            return $this->render('espaceCommande/index.html.twig', ['menus' => $menus]);
            }
        }
        return $this->render('espaceCommande/index.html.twig', [
            'menus' => $menus,
            'menu_id_selectionne' => $menuIdSelectionne
            ]);

    }
}