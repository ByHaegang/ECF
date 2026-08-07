<?php

namespace App\Controller;


use App\DAO\AvisDAO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/deconnexion', name: 'app_deconnexion', methods: ['POST'])]
public function logout(Request $request): Response
    {
    $session = $request->getSession();

    if (!$session->has('user_email')) {
        return $this->redirectToRoute('app_login');
    }

    $submittedToken = $request->request->get('_token');

    if (!$this->isCsrfTokenValid('logout', $submittedToken)) {
        $this->addFlash('attention', 'Jeton de sécurité invalide. Action annulée.');
        return $this->redirectToRoute('app_home');
    }

    $session->clear();
    $session->invalidate();

    $this->addFlash('success', 'Vous avez bien été déconnecté.');

    return $this->redirectToRoute('app_home');
    }
}