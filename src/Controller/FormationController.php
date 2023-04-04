<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\User;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    #[Route('/inscription/formation', name:'app_register_formation')]
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
                    $user->setAdress($form->get('adress')->getData());
                    $user->setCity($form->get('city')->getData());
                    $user->setPostalCode($form->get('postal_code')->getData());
                    $user->setTel('A modifier');
                    $siret = $form->get('siret')->getData();
                    $user->setUserFormation($formation);
                    $formation->setNumSiret($siret);
                    $formation->setNameRef('Ajouter le nom du contact');
                    $formation->setDomain('A modifier');
                    $formation->setWebSite('A modifier');
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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
