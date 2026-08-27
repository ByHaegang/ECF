<?php

namespace App\DAO;

use App\Document\StatsCommandes;
use Doctrine\ODM\MongoDB\DocumentManager;

class StatsCommandesDAO
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Enregistre une nouvelle statistique de commande dans MongoDB
     * @param int $orderId L'ID de la commande dans la base SQL
     * @param int $menuId L'ID du menu sélectionné
     * @param string $menuTitle Le titre du menu sélectionné
     * @param float $price Le prix total de la commande
     * @return void Ne renvoie rien, mais persiste la statistique dans MongoDB
     */
    public function ajouterStatistique(int $orderId, int $menuId, string $menuTitle, float $price): void
    {
        $stat = new StatsCommandes();
        $stat->setOrderId($orderId);
        $stat->setMenuId($menuId);
        $stat->setMenuTitle($menuTitle);
        $stat->setPrice($price);

        // persist "prépare" la sauvegarde, flush l'exécute dans la base
        $this->dm->persist($stat);
        $this->dm->flush();
    }

    /**
     * Récupère toutes les statistiques
     * @return StatsCommandes[] Retourne un tableau d'objets StatsCommandes
     */
    public function getAllStats(): array
    {
        return $this->dm->getRepository(StatsCommandes::class)->findAll();
    }

    /**
     * Récupère les statistiques pour un menu spécifique
     * @param int $menuId L'ID du menu pour lequel récupérer les statistiques
     * @return StatsCommandes[] Retourne un tableau d'objets StatsCommandes organisés par date décroissante
     */
    public function getStatsParMenuId(int $menuId): array
    {
        return $this->dm->getRepository(StatsCommandes::class)->findBy(
            ['menuId' => $menuId],
            ['createdAt' => 'DESC']
        );
    }
}