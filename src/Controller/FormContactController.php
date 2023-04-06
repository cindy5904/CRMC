<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormContactController extends AbstractController
{
    #[Route('/contact', name: 'app_form_contact')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
           
            
            
            $this->addFlash('success', 'Votre message a bien été envoyé!');
            
            return $this->redirectToRoute('app_form_contact');
        }
        return $this->render('form_contact/index.html.twig');
    }

}