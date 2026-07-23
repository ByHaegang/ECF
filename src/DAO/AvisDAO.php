<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class AvisDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Rècupère tous les avis
     * @return array La liste de tous les avis (tableaux associatifs)
     */

    public function getAllAvis() : array
    {
    $sql = "SELECT avis.note, avis.description, avis.statut, utilisateur.nom, utilisateur.prenom FROM avis 
        INNER JOIN utilisateur ON avis.utilisateur_id = utilisateur.utilisateur_id";


    $statement = $this->pdo->prepare("$sql");
    $statement->execute();

    return $statement->fetchall(\PDO::FETCH_ASSOC);
    }

    /**
     * Rècupère les avis validés
     * @return array La liste de tous les avis validés (tableaux associatifs)
     */
    public function getAvisValides() : array
    {
    $sql = "SELECT avis.note, avis.description, avis.statut, utilisateur.nom, utilisateur.prenom FROM avis 
        INNER JOIN utilisateur ON avis.utilisateur_id = utilisateur.utilisateur_id
        WHERE avis.statut = 'Validé'";


    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    
    return $statement->fetchall(\PDO::FETCH_ASSOC);
    }

    
}