<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstName', null, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un prénom'
                    ]),
                ],
            ])
            ->add('profession', null, [
                'label' => 'Profession',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer une profession'
                    ])
                ],
            ])
            ->add('status', null, [
                'label' => 'Status',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un status'
                    ]),
                ],
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
            ]);
        }      
}
