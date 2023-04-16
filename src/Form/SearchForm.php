<?php

namespace App\Form;

use App\SearchBar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('searchBar', TextType::class,[
            'required' => false,
            'label' => 'recherche',
            'attr' => [
                'placeholder' => 'Entreprise, centre de formation...',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchBar::class,
        ]);
    }
}