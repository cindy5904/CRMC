<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class ApplyController extends AbstractController
{
    #[Route('/apply/{id}', name: 'app_apply')]
    public function index(Request $request, EntityManagerInterface $entityManager, Publication $publication): Response
    {
        /** @var User */
        $apply = new Apply();
        $user = $this->getUser();
        dump($publication);
        $form = $this->createFormBuilder($apply)
            
            ->add('upload', FileType::class, array
            ('data_class' => null), [
                'mapped' => 'false',
                'required' => 'false',
                'constraints' => [
                    new File ([
                        'mimeTypes' => [
                            'cv.pdf',
                            'cv.word',
                        ]
                    ])
                ]
            ]
            )
            ->add('message', TextareaType::class, [
                'label' => 'Entrez Votre message',
                ]
            )
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $apply->setCreatedAt(new \DateTimeImmutable());
                $apply->setUser($user);
                $apply->setApplyPublication($publication);
                $posterFile = $form->get('upload')->getData();
                
                if ($posterFile) {
                    $fileName = uniqid().'.'.$posterFile->guessExtension(); 
                    $posterFile->move($this->getParameter('cv_upload'), $fileName);
                    
                   $apply->setUpload($fileName);
                }
                $entityManager->persist($apply);
                $entityManager->flush();
                
                $this->addFlash('success', sprintf('Votre candidature au poste "%s" a bien été prise en compte.', $publication->getTitle()));

                return $this->redirectToRoute('app_publication');
            }
        return $this->render('apply/index.html.twig',[
            'form' => $form,
            'post' => $publication,

        ]);
    }
}
