<?php

namespace App\Controller;

use App\DAO\UtilisateurDAO;
use App\DAO\CommandeDAO;
use App\DAO\AvisDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EspaceUtilisateurController extends AbstractController 
{
    #[Route('/espace_utilisateur', name: 'app_espace_utilisateur')]
    public function index(Request $request, CommandeDAO $commandeDAO, UtilisateurDAO $utilisateurDAO, AvisDAO $avisDAO): Response
    {
        $session = $request->getSession();
        $email = $session->get('user_email');

        if (!$email) {
            $this->addFlash('attention', 'Veuillez vous connecter pour accéder à cet espace.');
            return $this->redirectToRoute('app_login');
        }

        $utilisateur = $utilisateurDAO->getUtilisateurByEmail($email);
        
        if (!$utilisateur) {
            $session->clear();
            return $this->redirectToRoute('app_login');
        }

        $utilisateurId = $utilisateur['utilisateur_id'];

        $commandes = $commandeDAO->getCommandesParUtilisateur($utilisateurId);
        $avis = $avisDAO->getAvisParUtilisateur($utilisateurId);

        return $this->render('espaceUtilisateur/index.html.twig', [
            'utilisateur' => $utilisateur,
            'commandes' => $commandes,
            'avis' => $avis
        ]);
    }

    #[Route('/espace_utilisateur/commande/{id}/annuler', name: 'app_annuler_commande')]
    public function annulerCommande(int $id, Request $request, CommandeDAO $commandeDAO, UtilisateurDAO $utilisateurDAO): Response
    {
        $session = $request->getSession();
        $email = $session->get('user_email');

        if (!$email) {
            $this->addFlash('attention', 'Veuillez vous connecter pour effectuer cette action.');
            return $this->redirectToRoute('app_login');
        }

        $utilisateur = $utilisateurDAO->getUtilisateurByEmail($email);
        
        if (!$utilisateur) {
            $session->clear();
            return $this->redirectToRoute('app_login');
        }

        $utilisateurId = $utilisateur['utilisateur_id'];

        $annulationReussie = $commandeDAO->annulerCommandeParUtilisateur($id, $utilisateurId);

        if ($annulationReussie) {
            $this->addFlash('success', 'La commande #' . $id . ' a bien été annulée.');
        } else {
            $this->addFlash('error', 'Impossible d\'annuler cette commande. Elle est peut-être déjà traitée.');
        }

        return $this->redirectToRoute('app_espace_utilisateur');
    }

    #[Route('/espace_utilisateur/commande/{id}/modifier', name: 'app_modifier_commande', methods: ['POST'])]
    public function modifierCommande(
        int $id, 
        Request $request, 
        CommandeDAO $commandeDAO, 
        UtilisateurDAO $utilisateurDAO
    ): Response {
        $session = $request->getSession();
        $email = $session->get('user_email');

        if (!$email) {
            $this->addFlash('attention', 'Veuillez vous connecter pour effectuer cette action.');
            return $this->redirectToRoute('app_login');
        }

        $utilisateur = $utilisateurDAO->getUtilisateurByEmail($email);
        if (!$utilisateur) {
            $session->clear();
            return $this->redirectToRoute('app_login');
        }
        $utilisateurId = (int)$utilisateur['utilisateur_id'];

        $datePrestation = $request->request->get('date_prestation');
        $heureLivraison = $request->request->get('heure_livraison');
        $nombrePersonne = (int)$request->request->get('nombre_personne');
        $prixMenu       = (int)$request->request->get('prix_menu');

        $modificationReussie = $commandeDAO->modifierCommandeParUtilisateur(
            $id, 
            $utilisateurId, 
            $datePrestation, 
            $heureLivraison, 
            $nombrePersonne, 
            $prixMenu
        );

        if ($modificationReussie) {
            $this->addFlash('success', 'La commande #' . $id . ' a bien été modifiée.');
        } else {
            $this->addFlash('error', 'Impossible de modifier la commande. Vérifiez qu\'elle est toujours "En attente".');
        }

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}