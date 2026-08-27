<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class CommandeDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Enregistre une nouvelle commande dans la base de données.
     *
     * @param int $utilisateurId L'ID de l'utilisateur connecté.
     * @param int $menuId L'ID du menu sélectionné.
     * @param int $nombrePersonne Nombre de convives.
     * @param string $datePrestation Date de la prestation (format AAAA-MM-JJ).
     * @param string $heurePrestation Heure de la prestation (format HH:MM).
     * @param int $prixMenuEnCentimes Le prix total du menu calculé en centimes
     *
     * @return bool Retourne true si l'insertion a réussi, false en cas d'erreur.
     */
    public function ajouterCommande(int $utilisateurId, int $menuId, int $nombrePersonne, string $datePrestation, string $heurePrestation, int $prixMenuEnCentimes): bool
    {
        $dateCommande = (new \DateTime())->format('Y-m-d H:i:s');
        $prixLivraison = 0; 
        $statut = 'En attente'; 

        $sql = "INSERT INTO commande (date_commande, date_prestation, heure_livraison, prix_menu, nombre_personne, prix_livraison, statut, pret_materiel, restitution_materiel, utilisateur_id) 
                VALUES (:date_commande, :date_prestation, :heure_livraison, :prix_menu, :nombre_personne, :prix_livraison, :statut, FALSE, FALSE, :utilisateur_id)";

        $statement = $this->pdo->prepare($sql);

        $success = $statement->execute([
            'date_commande'   => $dateCommande,
            'date_prestation' => $datePrestation,
            'heure_livraison' => $heurePrestation,
            'prix_menu'       => $prixMenuEnCentimes,
            'nombre_personne' => $nombrePersonne,
            'prix_livraison'  => $prixLivraison,
            'statut'          => $statut,
            'utilisateur_id'  => $utilisateurId
        ]);

        if ($success) {
            return (int) $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * Recupere les commandes d'un utilisateur par son ID
     * 
     * @param int $utilisateurId Id de l'utilisateur
     * @return array Retourne les commandes de l'utilisateur sous forme de tableau associatif.
     */
    public function getCommandesParUtilisateur(int $utilisateurId): array
    {
        $sql = "SELECT * FROM commande WHERE utilisateur_id = :utilisateur_id ORDER BY date_commande DESC";
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['utilisateur_id' => $utilisateurId]);
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le détail d'une commande appartenant à un utilisateur par son Id de commande
     * 
     * @param int $commandeId Id de la commande
     * @param int $utilisateurId Id de l'utilisateur
     * @return array Retourne un tableau contenant toutes les informations de la commande;
     */
    public function getDetailCommande(int $commandeId, int $utilisateurId): ?array
    {
        $sql = "SELECT * FROM commande WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id";
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'commande_id' => $commandeId,
            'utilisateur_id' => $utilisateurId
        ]);
        
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Permet à l'utilisateur d'annuler sa commande lui-même.
     * 
     * @param int $commandeId L'ID de la commande.
     * @param int $utilisateurId L'ID de l'utilisateur.
     * @return bool True si la commande a été annulée, false sinon.
     */
    public function annulerCommandeParUtilisateur(int $commandeId, int $utilisateurId): bool
    {
        $this->pdo->beginTransaction();

        $sql = "UPDATE commande 
                SET statut = 'Annulée' 
                WHERE commande_id = :commande_id 
                    AND utilisateur_id = :utilisateur_id 
                    AND statut = 'En attente'";
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'commande_id' => $commandeId,
            'utilisateur_id' => $utilisateurId
        ]);

        // Si la commande n'existe pas ou n'est pas 'En attente'
        if ($statement->rowCount() === 0) {
            $this->pdo->rollBack();
            return false;
        }

        // Ajout dans l'historique de suivi
        $sql = "INSERT INTO suivi_commande (commande_id, statut) VALUES (:commande_id, 'Annulée')";
        $statementSuivi = $this->pdo->prepare($sql);
        $statementSuivi->execute(['commande_id' => $commandeId]);

        $this->pdo->commit();
        return true;
    }

    /**
     * Permet à l'utilisateur de modifier les détails de sa commande.
     * 
     * @param int $commandeId L'ID de la commande
     * @param int $utilisateurId L'ID de l'utilisateur
     * @param string $datePrestation La date de la prestation
     * @param string $heureLivraison L'heure de la livraison
     * @param int $nombrePersonne Le nombre de personnes
     * @param int $prixMenu Le prix du menu
     * @return bool True si la commande a été modifiée, false sinon
     */
    public function modifierCommandeParUtilisateur(int $commandeId, int $utilisateurId, string $datePrestation, string $heureLivraison, int $nombrePersonne, int $prixMenu): bool
    {
        // L'UPDATE ne s'exécutera QUE si la commande appartient à l'utilisateur ET qu'elle est toujours 'En attente'
        $sql = "UPDATE commande 
                SET date_prestation = :date_prestation, 
                    heure_livraison = :heure_livraison, 
                    nombre_personne = :nombre_personne, 
                    prix_menu = :prix_menu 
                WHERE commande_id = :commande_id 
                    AND utilisateur_id = :utilisateur_id 
                    AND statut = 'En attente'";
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'date_prestation' => $datePrestation,
            'heure_livraison' => $heureLivraison,
            'nombre_personne' => $nombrePersonne,
            'prix_menu'       => $prixMenu,
            'commande_id'     => $commandeId,
            'utilisateur_id'  => $utilisateurId
        ]);

        // Retourne true si 1 ligne a été modifiée (succès), false si 0 ligne touchée (échec/sécurité)
        return $statement->rowCount() > 0;
    }

    /**
     * Récupère l'intégralité des changements d'état d'une commande avec leurs dates.
     * 
     * @param int $commandeId L'ID de la commande
     * @return array La liste des étapes de suivi (tableaux associatifs)
     */
    public function getSuiviCommande(int $commandeId): array
    {
        $sql = "SELECT statut, date_modification 
                FROM suivi_commande 
                WHERE commande_id = :commande_id 
                ORDER BY date_modification ASC";
        
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['commande_id' => $commandeId]);
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}