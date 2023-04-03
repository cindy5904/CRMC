<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert as AssertAssert;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/connection/entreprise', name:'app_register_company')]
    public function registerCompany(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator)
    {   
        $form = $this->createFormBuilder()
            ->add('name', null, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un nom',
                    ])
                ],
            ])
            ->add('adress', null, [
                'label' => 'Adresse',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une adresse',
                    ])
                ],
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une ville',
                    ])
                ],
            ])
            ->add('postal_code', null, [
                'label' => 'Code Postal',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un code postal',
                    ])
                ],
            ])
            ->add('email')
            ->add('siret', IntegerType::class, [
                'label' => 'Numéro de siret',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisie obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 14,
                        'minMessage' => ''
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

            $entreprise = new Company();
            $user = new User();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
    
                $user->setRoles(['ROLE_USER']);
    
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email
    
                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }
    }
}
