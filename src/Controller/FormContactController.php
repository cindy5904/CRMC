<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormContactController extends AbstractController
{
    #[Route('/form/contact', name: 'app_form_contact')]
    public function index(): Response
    {
        return $this->render('form_contact/index.html.twig', [
            'controller_name' => 'FormContactController',
        ]);
    }
}
