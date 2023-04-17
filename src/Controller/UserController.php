<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    #[IsGranted('ROLE_USER')]
    public function index(UserRepository $userRepository): Response
    {
        $role = 'USER';
        $users = $userRepository->findByRole($role);

        return $this->render('user/profil.html.twig', [
            'user' => $userRepository->findByRole($role),
            'users' => $users,
            'role' => $role,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('profil/user/edit', name: 'app_edit')]
    public function edit(EntityManagerInterface $manager, Request $request): Response
    {
         // Formulaire modification de profil
        /** @var User */
        $user = $this->getUser();
        $logo = $user->getLogo();
        $formProfil = $this->createForm(UserProfileType::class, $user);

        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {

            $user->setEmail($formProfil->get('email')->getData());
            $user->setName($formProfil->get('name')->getData());
            $user->setAdress($formProfil->get('adress')->getData());
            $user->setPostalCode($formProfil->get('postalCode')->getData());
            $user->setCity($formProfil->get('city')->getData());
            $user->setTel($formProfil->get('tel')->getData());
            $user->setLogo($logo = $formProfil->get('logo')->getData());
            $user->setFirstName($formProfil->get('firstName')->getData());
            $user->setProfession($formProfil->get('profession')->getData());
            $user->setStatus($formProfil->get('status')->getData());

            if ($logo) {
                $fileName = uniqid().'.'.$logo->guessExtension();
                $logo->move($this->getParameter('profile_picture'), $fileName);
                $user->setLogo($fileName);
            }

            $manager->persist($user);
            $manager->flush();
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute('404 No Found');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'formProfil' => $formProfil,
        ]);
    }

    #[Route('/profil/user/{id}', name:'app_profil_user')]
    public function show($id,UserRepository $uR, PublicationRepository $publication): Response
    {
        /** @var User */
        $user = $this->getUser();
        $publications = $publication->findBy(['publicationUser' => $user]);

        $user = $uR->find($id);

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'publications' => $publications,
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
}