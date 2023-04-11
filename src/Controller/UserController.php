<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserProfileType;
use App\Repository\PublicationRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil/user', name:'app_profil_user')]
    public function show(PublicationRepository $publication): Response
    {
        /** @var User */
        $user = $this->getUser();
        $publications = $publication->findBy(['publicationUser' => $user]);

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'publications' => $publications
        ]);
    } 

    #[Route('/inscription/user', name:'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator)
    {
        $user = new User();

        $form = $this->createFormBuilder()
                ->add('name', null, [
                    'label' => 'Votre nom',
                    'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Veuillez entrer un nom',
                        ])
                    ],
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'constraints' => [
                        new Assert\Email([
                            'message' => 'Veuillez saisir un email valide',
                        ])
                    ],
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Assert\Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passse doit comporter au moins {{ limit }} caractères',
                            'max' => 4096,
                        ]),
                    ],
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => [
                        'label' => 'Confirmer le mot de passe',
                        'constraints' => [
                            new Assert\NotBlank([
                                'message' => 'Veuillez confirmer votre mot de passe',
                            ])
                        ],
                    ],
                ])
                ->add('agreeTerms', CheckboxType::class, [
                    'mapped' => false,
                    'label' => 'Accepter les conditions',
                    'constraints' => [
                        new Assert\IsTrue([
                            'message' => 'Vous devez accepter nos conditions',
                        ]),
                    ],
                ])
                ->getForm();

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    $user->setRoles(['ROLE_USER']);
                    $user->setEmail($form->get('email')->getData());
                    $user->setName($form->get('name')->getData());
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );
                }
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView()
                ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    #[Route('/profil/editProfile', name:'app_profil_editProfile')]
    public function editProfileUser(Request $request): Response
    {
        
        $user = $this->getUser();
        $form = $this->createForm(EditUserProfileType::class, $user);
    
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('user');
        }

        return $this->render('user/editProfile.html.twig', [
            'user' => $user
        ]);
    } 

    #[Route('/profil/upload-cv', name:'app_profil_upload_cv')]
    public function uploadCv(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('cv', FileType::class, [
            'label' => 'Télécharger votre CV',
        ])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cv = $form->get('cv')->getData();

            // Gérer le téléchargement du CV ici

            return $this->redirectToRoute('/profil/user');
        }

        return $this->render('profil/upload_cv.html.twig', [
        'form' => $form->createView(),
        ]);
    }
}