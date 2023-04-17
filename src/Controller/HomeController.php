<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $isHomepage = true;
            return $this->render('home/index.html.twig',[
                'is_homepage' => $isHomepage,
            ]);
    }

    #[Route('/inscription', name:'app_register_choice')]
    public function choice()
    {
        return $this->render('home/choice.html.twig');
    }

    #[Route('/mentions', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('rgpd/mentions-legale.html.twig');
    }


    #[Route('/pdc', name: 'app_pdc')]
    public function pdc(): Response
    {
        return $this->render('rgpd/pdc.html.twig');
    }

    #[Route('/aPropos', name: 'app_aPropos')]
    public function aPropos(): Response
    {
        return $this->render('home/aPropos.html.twig');
    }
}