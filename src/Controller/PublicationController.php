<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'app_publication')]
    public function index(PublicationRepository $publicationRepository, UserRepository $userRepository): Response
    {   
        $user = $this->getUser();
        $word = 'A modifier';
        $verify = $userRepository->verify($user, $word);
        $users = $userRepository->findAll();
        
        return $this->render('publication/index.html.twig', [
            'posts' => $publicationRepository->findBy([], ['createdAt' => 'DESC']),
            'users' => $users,
            'verify' => $verify,
        ]);
    }
}
