<?php

namespace App\Form;

use App\Entity\Accomodation;
use App\Entity\User;
use Doctrine\ORM\EntityRepository; // for 'address' query builder
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gla', EntityType::class, [
                // Multiple choice dropdown from User table, isGla = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->qbAllByCategory('gla');
                },
                'choice_label' => 'name',
                'label' => 'Provenance GLA :',
                'required' => false,
                'placeholder' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('volunteer', EntityType::class, [
                // Multiple choice dropdown from User table, isVolunteer = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->qbAllByCategory('volunteer');
                },
                'choice_label' => 'name',
                'label' => 'Bénévole :',
                'required' => false,
                'placeholder' => false,
                'multiple' => true,
                'expanded' => true           
            ])
            ->add('accomodation', EntityType::class, [
                // Dropdown from Accomodation table
                'class' => Accomodation::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.street', 'ASC');
                },
                'choice_label' => 'address',
                'label' => 'Adresse d\'intervention :',
                'required' => false,
                'placeholder' => false,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }
}
