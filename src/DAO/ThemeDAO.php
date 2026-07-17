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
     * Recupère tous les régimes dans la table theme
     * @return array La liste de tous les themes (tableau associatif)
    */

    public function getAllTheme(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM theme');
        $themes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $themes;
    }
}