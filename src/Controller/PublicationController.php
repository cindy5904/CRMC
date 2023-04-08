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
        /** @var User */
        $user = $this->getUser();
        $word = 'A modifier';
        $verify = $userRepository->verify($user, $word);
        $users = $userRepository->findAll();
        // récupération de tous les utilisateurs
        $publi = [];
        // initialise un tableau vide de publications
        foreach($users as $user){
            $roles = $user->getRoles();
            // récupération du role des utilisateurs
            if($roles[0] === 'ROLE_COMPANY' ||$roles[0] === 'ROLE_FORMATION'){
                //savoir si l'utilisateur récupéré est une entreprise ou centre de formation
                $value = $user->getId();
                // récupération de l'id de l'utilisateur entreprise ou centre de formation
                $publications = $publicationRepository->findBy(['publicationUser' => $value]);
                // récupération des publications (si plusieurs publication, on récupère un tableau de publication)
                foreach($publications as $publication){
                    // on récupère chaque publication
                    $publi[] = $publication;
                    // on injecte chaque publication dans le tableau des publications qu'on va passer à la vue
                } ;
            };
        };
        return $this->render('publication/index.html.twig', [
            'posts' => $publi,
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
