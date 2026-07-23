<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class ThemeDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Récupère tous les thèmes de la table theme
     * @return array La liste de tous les thèmes (tableaux associatifs)
     */
    public function getAllTheme(): array
    {
        $statement = $this->pdo->prepare('SELECT * FROM theme');
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}