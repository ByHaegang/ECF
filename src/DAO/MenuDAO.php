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
        $sql = 'SELECT menu.menu_id, menu.titre, menu.description, menu.prix_par_personne, 
                    menu.nombre_personne_minimum, menu.quantite_restante, 
                    theme.libelle AS theme_nom, regime.libelle AS regime_nom
                    FROM menu INNER JOIN theme ON menu.theme_id = theme.theme_id
                    INNER JOIN regime ON menu.regime_id = regime.regime_id';

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les menus filtrés
     * @param array $criteres Les critères de filtrage (tableau associatif)
     * @return array La liste des menus filtrés (tableau associatifs)
     */
    public function getMenusByFilter(array $criteres): array
    {
        $sql = 'SELECT menu.menu_id, menu.titre, menu.description, menu.prix_par_personne, 
                menu.nombre_personne_minimum, menu.quantite_restante, 
                theme.libelle AS theme_nom, regime.libelle AS regime_nom
            FROM menu 
            INNER JOIN theme ON menu.theme_id = theme.theme_id
            INNER JOIN regime ON menu.regime_id = regime.regime_id ';

        $conditions = [];
        $params = [];

        // Filtre de recherche textuelle (Recherche dans le titre OU la description)
        if (!empty($criteres['search'])) {
            $conditions[] = "(menu.titre LIKE :search OR menu.description LIKE :search)";
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
            // Si seul le prix minimum est rempli
            $conditions[] = "menu.prix_par_personne >= :fourchettePrixMin";
            $params[':fourchettePrixMin'] = $criteres['fourchettePrixMin'];
        } elseif (!empty($criteres['fourchettePrixMax'])) {
            // Si seul le prix maximum de la fourchette est rempli
            $conditions[] = "menu.prix_par_personne <= :fourchettePrixMax";
            $params[':fourchettePrixMax'] = $criteres['fourchettePrixMax'];
        }

        // Filtre par Régime (uniquement si ce n'est pas l'option vide "Tous les régimes")
        if (!empty($criteres['regime_id'])) {
            $conditions[] = "menu.regime_id = :regime_id";
            $params[':regime_id'] = $criteres['regime_id'];
        }

        // Filtre par Thème (uniquement si ce n'est pas l'option vide "Tous les thèmes")
        if (!empty($criteres['theme_id'])) {
            $conditions[] = "menu.theme_id = :theme_id";
            $params[':theme_id'] = $criteres['theme_id'];
        }

        // Si au moins un filtre est actif, on ajoute le "WHERE" et on assemble avec des "AND"
        if (count($conditions) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}


