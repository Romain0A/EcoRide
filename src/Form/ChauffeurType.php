<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Form\VoitureType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChauffeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('voiture', VoitureType::class, [
                'label' => false,
                'mapped' => false,
            ])
            ->add('fumeur', CheckboxType::class, [
                'label' => 'Accepte fumeur',
                'required' => false,
                'mapped' => false,
            ])
            ->add('animal', CheckboxType::class, [
                'label' => 'Accepte animal',
                'required' => false,
                'mapped' => false,
            ])
            ->add('preference_libre', TextType::class, [
                'label' => 'Autres préférences',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}