<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(UserRepository $userRepository): Response
    {
        $role = 'FORMATION';
        $usersFormation = $userRepository->findByRole($role);
        $users = $userRepository->findAll();
        
        return $this->render('formation/index.html.twig', [
            'usersFormation' => $usersFormation,
            'users' => $users,
            'role' => $role,
        ]);
    }

    #[Route('/inscription/formation', name: 'app_register_formation')]
    public function registerFormation(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator)
    {
        $user = new User();
        $formation = new Formation();

        $form = $this->createFormBuilder()
            ->add('name', null, [
                'label' => 'Nom du centre de formation',
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
            ->add('siret', NumberType::class, [
                'label' => 'Numéro de siret',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire',
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => 'Saisie minimum 14 chiffres',
                        'max' => 14,
                        'maxMessage' => 'Saisie maximum 14 chiffres'
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

            $user->setRoles(['ROLE_FORMATION']);
            $user->setEmail($form->get('email')->getData());
            $user->setName($form->get('name')->getData());
            $siret = $form->get('siret')->getData();
            $user->setUserFormation($formation);
            $formation->setNumSiret($siret);
            $entityManager->persist($user);
            $entityManager->persist($formation);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('registration/register-formation.html.twig', [
            'registrationFormFormation' => $form->createView()
        ]);
    }

    #[Route('/formation/profil/{id}', name: 'app_formation_profil')]
    public function show($id,UserRepository $uR, PublicationRepository $publication, Request $request, EntityManagerInterface $manager)
    {
        // Afficher les publications de l'utilisateur
        /** @var User */
        $user = $this->getUser();
        $publications = $publication->findBy(['publicationUser' => $user], ['createdAt' => 'DESC']);

        $user = $uR->find($id);

        // Formulaire ajouter une publication
        $post = new Publication();
        $form = $this->createForm(PublicationType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setPublicationUser($this->getUser());

            $manager->persist($post);
            $manager->flush();
        }

        return $this->render('formation/profil.html.twig', [
            'user' => $user,
            'posts' => $publications,
            'form' => $form,
        ]);
    }

    #[Route('/formation/modify', name: 'app_formation_modify')]
    public function modify(Request $request, EntityManagerInterface $manager): Response
    {
        // Formulaire modification de profil
        /** @var User */
        $user = $this->getUser();
        $formation = $user->getUserFormation();
        $logo = $user->getLogo();
        $formProfil = $this->createFormBuilder([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'adress' => $user->getAdress(),
            'postalCode' => $user->getPostalCode(),
            'city' => $user->getCity(),
            'siret' => $formation->getNumSiret(),
            'nameRef' => $formation->getNameRef(),
            'description' => $formation->getDescription(),
            'domain' => $formation->getDomain(),
            'webSite' => $formation->getWebSite(),
            'tel' => $user->getTel(),
            'logo'=> $user->getLogo(),
           ])

            ->add('siret', NumberType::class, [
                'label' => 'N° de siret',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un n° de siret'
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => 'Saisie minimum 14 chiffres',
                        'max' => 14,
                        'maxMessage' => 'Saisie maximum 14 chiffres'
                    ])
                ],
            ])
            ->add('nameRef', null, [
                'label' => 'Nom de la personne référente',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un nom'
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du centre de formation',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une description'
                    ]),
                    new Assert\Length([
                        'min' => 100,
                        'minMessage' => '100 caractères minimum'
                    ])
                ],
            ])
            ->add('domain', null, [
                'label' => 'Domaine d\'expertise',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre domaine'
                    ])
                ],
            ])
            ->add('webSite', UrlType::class, [
                'label' => 'Lien de votre site web',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ])
                ]
            ])
            ->add('name', null, [
                'label' => 'Nom du centre de formation',
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
                        'message' => 'Saisir un email valide'
                    ])
                ],
            ])
            ->add('adress', null, [
                'label' => 'N° et voie',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuiilez renseigner une adresse'
                    ])
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'Code Postal',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner un code postal'
                    ])
                ],
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner une ville'
                    ])
                ],
            ])
            ->add('tel', null, [
                'label' => 'N° de téléphone',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un numéro de téléphone'
                    ])
                ],
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/jfif',
                        ]
                    ])
                ],
            ])
        
            // ->add('partenaires', CheckboxType::class, [
            //     'label' => 'Souhaitez-vous devenir partenaire ?'
            // ])
            ->getForm();
        $formProfil->handleRequest($request);

        if ($formProfil->isSubmitted() && $formProfil->isValid()) {

            $user->setEmail($formProfil->get('email')->getData());
            $user->setName($formProfil->get('name')->getData());
            $user->setAdress($formProfil->get('adress')->getData());
            $user->setPostalCode($formProfil->get('postalCode')->getData());
            $user->setCity($formProfil->get('city')->getData());
            $user->setTel($formProfil->get('tel')->getData());
            $user->setLogo($logo = $formProfil->get('logo')->getData());

            if ($logo) {
                $fileName = uniqid().'.'.$logo->guessExtension();
                $logo->move($this->getParameter('images_directory'), $fileName);
                $user->setLogo($fileName);
            }

            $formation = $user->getUserFormation();
            $formation->setNumSiret($formProfil->get('siret')->getData());
            $formation->setNameRef($formProfil->get('nameRef')->getData());
            $formation->setDescription($formProfil->get('description')->getData());
            $formation->setDomain($formProfil->get('domain')->getData());
            $formation->setWebSite($formProfil->get('webSite')->getData());
            $manager->persist($user);
            $manager->flush();
        }
        return $this->render('formation/modify.html.twig', [
            'user' => $user,
            'formProfil' => $formProfil,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
