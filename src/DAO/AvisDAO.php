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
      $statement = $this->pdo->query('SELECT * FROM avis');
      return $statement->fetchall(\PDO::FETCH_ASSOC);
      }

    /**
     * Rècupère les avis validés
     * @return array La liste de tous les avis validés (tableaux associatifs)
     */
    public function getAvisValides() : array
    {
      $statement = $this->pdo->query("SELECT * FROM avis WHERE statut = 'validé'");
      return$statement->fetchall(\PDO::FETCH_ASSOC);
    }

    
}