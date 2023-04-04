<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyController extends AbstractController
{   
    #[Route('/entreprise', name:'app_company')]
    public function index()
    {
        return $this->render('company/index.html.twig');
    }

    #[Route('/entreprise/profil', name: 'app_company_profil')]
    public function show(): Response
    {   
        $user = $this->getUser();
        return $this->render('company/show.html.twig', [
            'user' => $user
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
}
