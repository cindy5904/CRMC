<?php

namespace App\Controller;

use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PublicationController extends AbstractController
{   
    #[IsGranted('ROLE_USER')]
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

    #[IsGranted('ROLE_USER')]
    #[Route('publication/{id}', name: 'app_publication_one')]
    public function showOne(PublicationRepository $publicationRepository, $id)
    {   
        $posts = $publicationRepository->findBy(['id' => $id]);
        foreach($posts as $post){
            $post;
        }
        $user = $post->getPublicationUser();
        $company = $user->getUserEntreprise();
        $id = $company->getId();

        return $this->render('publication/showOne.html.twig',[
            'post' => $post,
            'id' => $id,
            'company' => $company
        ]);
    }
}
