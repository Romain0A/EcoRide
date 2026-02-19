<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depart')
            ->add('heure_depart')
            ->add('lieu_depart')
            ->add('date_arrivee')
            ->add('heure_arrivee')
            ->add('lieu_arrivee')
            ->add('nb_place')
            ->add('prix_personne')
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => function (Voiture $voiture) {
                    return $voiture->getMarque() . ' ' . $voiture->getModele() . ' (' . $voiture->getImmatriculation() . ')';
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('v')
                        ->where('v.utilisateur = :user')
                        ->setParameter('user', $options['user']);
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
            'user' => null,
        ]);
    }
}
