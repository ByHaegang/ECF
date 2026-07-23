<?php

namespace App\DAO;

use Doctrine\DBAL\Connection;

class MenuDAO
{
    private \PDO $pdo;

    public function __construct(Connection $connection)
    {
        // On récupère l'accès natif à PDO
        $this->pdo = $connection->getNativeConnection();
    }

    /**
     * Récupère tous les menus
     * @return array La liste de tous les menus (tableau associatifs)
     */
    public function getAllMenus(): array
    {
        $sql = "SELECT menu.menu_id, menu.titre, menu.description, menu.prix_par_personne, 
                menu.nombre_personne_minimum, menu.quantite_restante,
                theme.libelle AS theme_nom, regime.libelle AS regime_nom,
                STRING_AGG(DISTINCT allergene.libelle, ', ') AS allergenes
                
                FROM menu
                INNER JOIN theme ON menu.theme_id = theme.theme_id
                INNER JOIN regime ON menu.regime_id = regime.regime_id
                LEFT JOIN propose ON menu.menu_id = propose.menu_id
                LEFT JOIN plat ON propose.plat_id = plat.plat_id
                LEFT JOIN contient ON plat.plat_id = contient.plat_id
                LEFT JOIN allergene ON contient.allergene_id = allergene.allergene_id
                GROUP BY menu.menu_id, theme.libelle, regime.libelle";

        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le prix d'un menu par son ID.
     * @param int $menu_id L'ID du menu.
     * @return int Le prix du menu en centimes.
     */
    public function getMenuPriceById(int $menu_id): int
    {
        $statement = $this->pdo->prepare("SELECT prix_par_personne FROM menu WHERE menu_id = :menu_id");
        $statement->execute(['menu_id' => $menu_id]);

        $result = $statement->fetchColumn();
        return (int) $result;
    }

    /**
     * Récupère les menus filtrés
     * @param array $criteres Les critères de filtrage (tableau associatif)
     * @return array La liste des menus filtrés (tableau associatifs)
     */
    public function getMenusByFilter(array $criteres): array
    {
        $sql = "SELECT menu.menu_id, menu.titre, menu.description, menu.prix_par_personne, 
                menu.nombre_personne_minimum, menu.quantite_restante,
                theme.libelle AS theme_nom, regime.libelle AS regime_nom,
                STRING_AGG(DISTINCT allergene.libelle, ', ') AS allergenes
                
                FROM menu
                INNER JOIN theme ON menu.theme_id = theme.theme_id
                INNER JOIN regime ON menu.regime_id = regime.regime_id
                LEFT JOIN propose ON menu.menu_id = propose.menu_id
                LEFT JOIN plat ON propose.plat_id = plat.plat_id
                LEFT JOIN contient ON plat.plat_id = contient.plat_id
                LEFT JOIN allergene ON contient.allergene_id = allergene.allergene_id";

        $conditions = [];
        $params = [];

        // Filtre de recherche textuelle (Recherche dans le titre OU la description)
        if (!empty($criteres['search'])) {
            $conditions[] = "(menu.titre ILIKE :search OR menu.description ILIKE :search)";
            $params[':search'] = '%' . $criteres['search'] . '%';
        }

        // Filtre Prix Maximum
        if (!empty($criteres['prixMax'])) {
            $conditions[] = "menu.prix_par_personne <= :prixMax";
            $params[':prixMax'] = $criteres['prixMax'];
        }

        // Filtre Fourchette de prix (Min et Max)
        if (!empty($criteres['fourchettePrixMin']) && !empty($criteres['fourchettePrixMax'])) {
            $conditions[] = "menu.prix_par_personne BETWEEN :fourchettePrixMin AND :fourchettePrixMax";
            $params[':fourchettePrixMin'] = $criteres['fourchettePrixMin'];
            $params[':fourchettePrixMax'] = $criteres['fourchettePrixMax'];
        } elseif (!empty($criteres['fourchettePrixMin'])) {
            $conditions[] = "menu.prix_par_personne >= :fourchettePrixMin";
            $params[':fourchettePrixMin'] = $criteres['fourchettePrixMin'];
        } elseif (!empty($criteres['fourchettePrixMax'])) {
            $conditions[] = "menu.prix_par_personne <= :fourchettePrixMax";
            $params[':fourchettePrixMax'] = $criteres['fourchettePrixMax'];
        }

        // Filtre par Régime
        if (!empty($criteres['regime_id'])) {
            $conditions[] = "menu.regime_id = :regime_id";
            $params[':regime_id'] = $criteres['regime_id'];
        }

        // Filtre par Thème
        if (!empty($criteres['theme_id'])) {
            $conditions[] = "menu.theme_id = :theme_id";
            $params[':theme_id'] = $criteres['theme_id'];
        }

        if (count($conditions) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= " GROUP BY menu.menu_id, theme.libelle, regime.libelle";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}