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
                VALUES (:date_commande, :date_prestation, :heure_livraison, :prix_menu, :nombre_personne, :prix_livraison, :statut, 0, 0, :utilisateur_id)";

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'date_commande'   => $dateCommande,
            'date_prestation' => $datePrestation,
            'heure_livraison' => $heurePrestation,
            'prix_menu' => $prixMenuEnCentimes,
            'nombre_personne' => $nombrePersonne,
            'prix_livraison' => $prixLivraison,
            'statut' => $statut,
            'utilisateur_id' => $utilisateurId
        ]);
    }
}