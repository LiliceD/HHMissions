<?php

namespace App\Form;

use App\Entity\Address;
use App\Utils\Constant;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // for 'gla' drop-down;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
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
            ->add('zipCode', null, array('label' => 'Code postal :'))
            ->add('city', null, array('label' => 'Ville :'))
            ->add('ownerType', ChoiceType::class, array(
                'choices' => Constant::getOwnerTypes(),
                'label' => 'Propriétaire :',
                'placeholder' => '-'
            ))
            ->add('access', null, array('label' => 'Accès à l\'immeuble (facultatif) :', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
        ));
    }
}
