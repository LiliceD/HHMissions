<?php

namespace App\Form;

use App\Entity\Accomodation;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // for 'gla' drop-down;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // postal code
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccomodationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Nom (facultatif) :',
                'attr' => array('placeholder' => 'Exemple : Le Chorus'),
                'required' => false
            ))
            ->add('street', null, array('label' => 'Adresse :'))
            ->add('postalCode', null, array('label' => 'Code postal :'))
            ->add('city', null, array('label' => 'Ville :'))
            ->add('ownerType', ChoiceType::class, array(
                'choices' => Accomodation::getOwnerTypes(),
                'label' => 'Propriétaire :',
                'placeholder' => '-'
            ))
            ->add('access', null, array('label' => 'Accès :', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Accomodation::class,
        ));
    }
}