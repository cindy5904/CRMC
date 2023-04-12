<?php

namespace App\Form;

use App\SearchSelectFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSelect extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('types', ChoiceType::class,[
            'choices' => [
            'Stage' => 'stage',
            'Alternance' => 'alternance',
            'Emploi' => 'emploi',
            ],
            'required' => false,
        ])
        ->add('profession', ChoiceType::class, [
            'choices' => [
                'Designer UX' => 'Designer UX',
                'Développeur Web' => 'Développeur Web',
                'Analyste de données' => 'Analyste de Données',
                'Chef de Projet' => 'Chef de Projet',
                'Ingénieur Logitiel' => 'Ingénieur Logiciel',
                'Spécialiste en Sécurité Informatique' => 'Spécialiste en Sécurité Informatique',
                'Architecte Cloud' => 'Architect Cloud',
                'Administrateur de Bases de Données' => 'Administrateur Base de Données',
                'Développeur Mobile' => 'Développeur Mobile',
                'Expert en Réseaux Informatiques' => 'Expert en Réseaux Informatiques'
            ],
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchSelectFilter::class,
        ]);
    }
}