<?php

namespace App\Controller;

use App\Form\SearchForm;
use App\Form\SearchSelect;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\SearchBar;
use App\SearchSelectFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PublicationController extends AbstractController
{   
    #[IsGranted('ROLE_USER')]
    #[Route('/publication', name: 'app_publication')]
    public function index(PublicationRepository $publicationRepository, UserRepository $userRepository,Request $request): Response
    {   
        $user = $this->getUser();
        $word = 'A modifier';
        $verify = $userRepository->verify($user, $word);
        $users = $userRepository->findAll();
        $search = new SearchBar();
        $select = new SearchSelectFilter();
        $posts = $publicationRepository->findBy([], ['createdAt' => 'DESC']);
        $form = $this->createForm(SearchForm:: class, $search);
        $form->handleRequest($request);
        $formSelect = $this->createForm(SearchSelect::class, $select);
        $formSelect->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $posts= $publicationRepository->findBySearch($form->getData());
        }

        if($formSelect->isSubmitted() && $formSelect->isValid()){
            $results = $formSelect->getData();
            $posts= $publicationRepository->findByType($results->getProfession(), $results->getTypes());
        }

        return $this->render('publication/index.html.twig', [
            'posts' => $posts,
            'form' => $form,
            'users' => $users,
            'verify' => $verify,
            'formSelect' => $formSelect,
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
        $formation = $user->getUserFormation();
        dump($post->getPublicationUser());
        // if($post->getPublicationCompany() != null){
        //     $idCompany = $company->getId();
        //     dump($post);
        //     dump($post->getPublicationCompany());
        //     return $this->render('publication/showOne.html.twig',[
        //         'post' => $post,
        //         'idCompany' => $idCompany,
        //         'company' => $company,
        //     ]);
        // }

        // if($post->getPublicationFormation() != null){
        //     $idFormation = $formation->getId();
        //     return $this->render('publication/showOne.html.twig',[
        //         'post' => $post,
        //         'idFormation' => $idFormation,
        //         'formation' => $formation
        //     ]);
        // }
        return $this->render('publication/showOne.html.twig',[
            'post' => $post,
            'user' => $user,
        ]);
    }
}
