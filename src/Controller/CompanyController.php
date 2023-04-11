<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\PublicationType;
use App\Repository\CompanyRepository;
use App\Repository\PublicationRepository;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class CompanyController extends AbstractController
{   
    #[Route('/entreprise/detail/{id}', name:'app_company')]
    public function index($id,CompanyRepository $cr,UserRepository $ur, PublicationRepository $pr)
    {   
        $userCompany = $cr->findBy(['id' => $id]);
        // récupération de la company à l'origine de la publication (donc siret, description..)
        $users = $ur->findBy(['userEntreprise' => $id]);
        // récupération des données utilisateurs (donc name, adress..)
        foreach($userCompany as $company){
            $company;
        }
        foreach($users as $user){
            $user;
        }
        // récupération des publications de l'utilisateur en fonction de son id
        $publiId = $user->getId();
        $publications = $pr->findBy(['publicationUser' => $publiId], ['createdAt' => 'DESC']);
       
        return $this->render('company/index.html.twig', [
            'company' => $company,
            'publications' => $publications,
            'user' => $user
        ]);
    }

    #[Route('/entreprise/profil', name: 'app_company_profil')]
    public function show(PublicationRepository $publi): Response
    {   
        /** @var User */
        $user = $this->getUser();
        $id = $user->getId();
        dump($id);
        $publications = $publi->findBy(['publicationUser' => $id], ['createdAt' => 'DESC']);
        dump($publications);

        return $this->render('company/show.html.twig', [
            'publications' => $publications,
            'user' => $user
        ]);
    }

    #[Route('entreprise/edit', name:'app_company_edit')]
    public function edit(Request $request,EntityManagerInterface $entityManager)
    {   
        /** @var User $user */
       $user = $this->getUser();

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
            ->add('postalCode', NumberType::class, [
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
           ->add('domaine', null,[
               'label' => 'Saisir votre champ d\'expertise',
               'constraints' => [
                   new Assert\NotBlank([
                       'message' => 'Veuillez entrer votre domaine'
                   ])
               ]
           ])
           ->add('logo', FileType::class, array('data_class' => null),[
               'mapped' => 'false',
               'required' => 'false',
               'constraints' => [
                   new File([
                       'mimeTypes' => [
                           'image/jpeg',
                           'image/jpg',
                           'image/png',
                           'image/jfif',
                       ]
                   ])
               ]
           ])
           ->add('partenaires', CheckboxType::class, [
               'label' => 'souhaitez vous devenir partenaires ?'
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

           $form->handleRequest($request);
           if ($form->isSubmitted() && $form->isValid()) {
               $user->setEmail($form->get('email')->getData());
               $user->setName($form->get('name')->getData());
               $user->setAdress($form->get('adress')->getData());
               $user->setPostalCode($form->get('postalCode')->getData());
               $user->setCity($form->get('city')->getData());
               $company = $user->getUserEntreprise();
               $company->setNameRef($form->get('nameRef')->getData());
               $company->setNumSiret($form->get('siret')->getData());
               $company->setDescription($form->get('description')->getData());
               $company->setDomaine($form->get('domaine')->getData());
               $company->setPartenaire($form->get('partenaires')->getData());
               $company->setWebSite($form->get('webSite')->getData());
               dump($user);
               $entityManager->persist($user);
               $entityManager->flush();
   
           }

       return $this->render('company/edit.html.twig', [
           'user' => $user,
           'form' => $form
       ]);
    }
    #[Route('/inscription/entreprise', name:'app_register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator)
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
                'registrationFormCompany' => $form->createView()
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    #[Route('/entreprise/createpost', name:'app_company_createpost')]
    public function create(Request $request, EntityManagerInterface $manager, PublicationRepository $publication): Response
    {
        
        $publication = new Publication();
        $form = $this->createFormBuilder($publication)
        ->add('title', null, [
            'label'=> 'Titre de la publication',
        ])
        ->add('type')
        ->add('content', null, [
            'label' => 'Contenu de la publication',
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form['content']->getData();
            $publication->setTitle($form->get('title')->getData());
            $publication->setCreatedAt(new \DateTimeImmutable('now'));
            $publication->setContent($data);
            $publication->setType($form->get('type')->getData());
            $publication->setPublicationUser($this->getUser());
            // $publication->setPublicationCompany($this->getUser()->getUserEntreprise());
            $manager->persist($publication);
            $manager->flush();

            return $this->redirectToRoute('app_publication');
        }    
        
    
    return $this->render('company/create-post.html.twig', [
        'publication' =>$publication,
        'form' => $form->createView(),
    ]);
    }
}
