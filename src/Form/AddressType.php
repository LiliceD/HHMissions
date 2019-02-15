<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // for 'gla' drop-down;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddressType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
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
                'choices' => Address::getOwnerTypes(),
                'label' => 'Propriétaire :',
                'placeholder' => '-'
            ))
            ->add('access', null, array('label' => 'Accès à l\'immeuble (facultatif) :', 'required' => false))
            ->add('gla', EntityType::class, [
                // Dropdown from User table, gla = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(User::CATEGORY_GLA);
                },
                'choice_label' => 'name',
                'label' => 'Responsable GLA :',
                'placeholder' => '-',
            ])
            ->add('referent', EntityType::class, [
                // Dropdown from User table, volunteer = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(User::CATEGORY_VOLUNTEER);
                },
                'choice_label' => 'name',
                'label' => 'Référent immeuble :',
                'placeholder' => '-',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
        ));
    }
}
