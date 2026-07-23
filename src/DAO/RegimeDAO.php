<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class RegimeDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Récupère tous les régimes dans la table regime
     * 
     * @return array La liste de tous les régimes (tableau associatif)
     */
    public function getAllRegime(): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM regime');
        $statement->execute();
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}