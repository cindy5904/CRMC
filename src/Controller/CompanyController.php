<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\ApplyRepository;
use App\Repository\CompanyRepository;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class CompanyController extends AbstractController
{   #[IsGranted('ROLE_USER')]
    #[Route('/entreprise', name:'app_company')]
    public function index(
        UserRepository $ur,
        Request $request,
        PaginatorInterface $paginator,
        )
    {
        $role = 'COMPANY';
        $company = $ur->findByRole($role);
        $totalCount = ceil((count($company))/10);
        $page = $request->query->get('page', 1);

        if ($page > $totalCount) {
            throw $this->createNotFoundException("La page $page est inexistante.");
        }
        $company = $paginator->paginate(
            $company,
            $page,
            10
        );

        return $this->render('company/index.html.twig', [
            'companys' => $company
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/entreprise/detail/{id}', name:'app_company_retail')]
    public function showOne(
        $id,
        CompanyRepository $cr,
        UserRepository $ur,
        PublicationRepository $pr,
        )
    {
        $userCompany = $cr->findBy(['id' => $id]);
        // récupération de la company à l'origine de la publication (donc siret, description..)
        $users = $ur->findBy(['userEntreprise' => $id]);
        // récupération des données utilisateurs (donc name, adress..)
        foreach ($userCompany as $company) {
            $company;
        }
        foreach ($users as $user) {
            $user;
        }
        // récupération des publications de l'utilisateur en fonction de son id
        $publiId = $user->getId();
        $publications = $pr->findBy(['publicationUser' => $publiId], ['createdAt' => 'DESC']);

        return $this->render('company/showOne.html.twig', [
            'company' => $company,
            'publications' => $publications,
            'user' => $user
        ]);
    }

    #[IsGranted('ROLE_COMPANY')]
    #[Route('/entreprise/profil', name: 'app_company_profil')]
    public function show(
        Request $request,
        PublicationRepository $publi,
        EntityManagerInterface $manager,
        ApplyRepository $ar,
        UserRepository $ur,
        ): Response
    {
        /** @var User */
        $user = $this->getUser();
        $id = $user->getId();
        $publications = $publi->findBy(['publicationUser' => $id], ['createdAt' => 'DESC']);
        $publication = new Publication();
        $form = $this->createFormBuilder($publication)
            ->add('title', null, [
                'label' => 'Titre de la publication',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => 'Saisie minimum 2 caractères',
                        'max' => 100,
                        'maxMessage' => 'Saisie maximum 100 caractères'
                    ])
                ]
            ])
            ->add('type', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la publication',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une description'
                    ]),
                    new Assert\Length([
                        'min' => 100,
                        'minMessage' => '100 caractères minimum'
                    ])
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setCreatedAt(new \DateTimeImmutable('now'));
            $publication->setPublicationUser($this->getUser());
            $publication->setPublicationCompany($user->getUserEntreprise());

            $manager->persist($publication);
            $manager->flush();

            return $this->redirectToRoute('app_publication');
        }

        /** @var User  */
        $user = $this->getUser();
        $company = $user->getUserEntreprise();
        $form1 = $this->createFormBuilder([
            'name' => $company->getName(),
            'email' => $user->getEmail(),
            'adress' => $user->getAdress(),
            'postalCode' => $user->getPostalCode(),
            'city' => $user->getCity(),
            'siret' => $company->getNumSiret(),
            'nameRef' => $company->getNameRef(),
            'description' => $company->getDescription(),
            'domaine' => $company->getDomaine(),
            'webSite' => $company->getWebSite(),
        ])
            ->add('name', null, [
                'label' => 'Nom de l\'entreprise',
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
                ]
            ])
            ->add('adress', null, [
                'label' => 'N° et voie',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner une adresse'
                    ])
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner un code postal'
                    ])
                ]
            ])
            ->add('city', null, [
                'label' => 'Ville/commune',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez renseigner une ville'
                    ])
                ]
            ])
            ->add('siret', NumberType::class, [
                'label' => 'Numéro de siret',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => 'Saisie minimum 14 chiffre',
                        'max' => 14,
                        'maxMessage' => 'Saisie maximum 14 chiffre'
                    ])
                ]
            ])
            ->add('nameRef', null, [
                'label' => 'Personne de référence',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entre un nom'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre entreprise',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une description'
                    ]),
                    new Assert\Length([
                        'min' => 100,
                        'minMessage' => '100 caractères minimum'
                    ])
                ]
            ])
            ->add('domaine', null, [
                'label' => 'Saisir votre champ d\'expertise',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer votre domaine'
                    ])
                ]
            ])
            ->add('logo', FileType::class, array(
                'data_class' => null,
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
            ))
            ->add('partenaires', CheckboxType::class, [
                'label' => 'Devenir partenaire',
                'required' => false
            ])
            ->add('webSite', UrlType::class, [
                'label' => 'Lien de votre site web',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ])
                ]
            ])
            ->getForm();
            $form1->handleRequest($request);
            if ($form1->isSubmitted() && $form1->isValid()) {
                $user->setEmail($form1->get('email')->getData());
                $user->setName($form1->get('name')->getData());
                $user->setAdress($form1->get('adress')->getData());
                $user->setPostalCode($form1->get('postalCode')->getData());
                $user->setCity($form1->get('city')->getData());
                $company = $user->getUserEntreprise();
                $company->setNameRef($form1->get('nameRef')->getData());
                $company->setName($form1->get('name')->getData());
                $company->setNumSiret($form1->get('siret')->getData());
                $company->setDescription($form1->get('description')->getData());
                $company->setDomaine($form1->get('domaine')->getData());
                $company->setPartenaire($form1->get('partenaires')->getData());
                $company->setWebSite($form1->get('webSite')->getData());
                $logo = $form1->get('logo')->getData();
                if ($logo) {
                    $fileName = uniqid().'.'.$logo->guessExtension();
                    $logo->move($this->getParameter('profile_picture'), $fileName);
                    $user->setLogo($fileName);
                };
                $manager->persist($user);
                $manager->flush();

            return $this->redirectToRoute('app_company_profil');
        }

        $candidat = [];
        foreach($publications as $publication){
            $id = $publication->getId();
            $publi = $ar->findPostulaCandidat($id);;
            $candidat[] = $publi;
        }

        return $this->render('company/show.html.twig', [
            'publications' => $publications,
            'user' => $user,
            'form' => $form,
            'form1' => $form1,
            'company' =>$company,
            'publication' => $candidat,
        ]);
    }
    #[Route('/inscription/entreprise', name:'app_register_company')]
    public function registerCompany(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator)
    {
        $user = new User();
        $company = new Company();

        $form = $this->createFormBuilder()
            ->add('name', null, [
                'label' => 'Nom de l\'entreprise',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un nom',
                    ])
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                    new Assert\Email([
                        'message' => 'Saisir un email valide'
                    ])
                ]
            ])
            ->add('siret', NumberType::class, [
                'label' => 'Numéro de siret',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => 'Saisie minimum 14 chiffre',
                        'max' => 14,
                        'maxMessage' => 'Saisie maximum 14 chiffre'
                    ])
                ]
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
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
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
                        'message' => 'Vous devez accepter nos conditions.',
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

            $user->setRoles(['ROLE_COMPANY']);
            $user->setEmail($form->get('email')->getData());
            $user->setName($form->get('name')->getData());
            $siret = $form->get('siret')->getData();
            $user->setUserEntreprise($company);
            $company->setNumSiret($siret);
            $company->setName($form->get('name')->getData());
            $entityManager->persist($user);
            $entityManager->persist($company);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('registration/register-company.html.twig', [
            'registrationFormCompany' => $form
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
