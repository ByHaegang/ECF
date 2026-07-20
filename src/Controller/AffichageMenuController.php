<?php

namespace App\Controller;

use App\DAO\MenuDAO;
use App\DAO\RegimeDAO;
use App\DAO\ThemeDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AffichageMenuController extends AbstractController
{
    #[route('/affichage-menu', name: 'app_affichage_menu')]
    public function index(Request $request, MenuDAO $menuDAO, RegimeDAO $regimeDAO, ThemeDAO $themeDAO): Response
    {
        
        if ($request->isMethod('POST')) {
            $criteres = $request->request->all();
            $menus = $menuDAO->getMenusByFilter($criteres);

        if ( empty($menus)) {
            $this->AddFlash('Attention','Désolé, aucun menu ne satisfait les filtres.');
        }

        } else {
            $menus = $menuDAO->getAllMenus();
        }

        $themes = $themeDAO->getAllTheme();
        $regimes = $regimeDAO->getAllRegime();

        return $this->render('affichageMenu/index.html.twig', [
            'menus' => $menus,
            'regimes' => $regimes,
            'themes' => $themes
        ]);
    }
} 