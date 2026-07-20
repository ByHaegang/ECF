<?php

namespace App\Controller;


use App\DAO\AvisDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(AvisDAO $avisDao): Response
    {
        $avis = $avisDao->getAvisValides();

        return $this->render('home/index.html.twig', [
            'liste_avis' => $avis
        ]);
    }
}