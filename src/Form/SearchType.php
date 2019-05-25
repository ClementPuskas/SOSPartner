<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class, [
                'required' => false,
                'label' => 'Nom du joueur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer le nom du joueur'
                ]
            ])
            ->add('prenom',TextType::class, [
                'required' => false,
                'label' => 'Prénom du joueur',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrer le prénom du joueur'
                ]
            ])
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
