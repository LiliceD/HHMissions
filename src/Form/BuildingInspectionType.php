<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\BuildingInspection;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuildingInspectionType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gla', EntityType::class, [
                // Dropdown from User table, gla = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(User::CATEGORY_GLA);
                },
                'choice_label' => 'name',
            ])
            ->add('gla_name', TextType::class, [
                'label' => 'Responsable GLA :',
                'disabled' => true,
                'mapped' => false,
            ])
            ->add('referent', EntityType::class, [
                // Dropdown from User table, volunteer = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(User::CATEGORY_VOLUNTEER);
                },
                'choice_label' => 'name',
            ])
            ->add('referent_name', TextType::class, [
                'label' => 'Référent immeuble :',
                'disabled' => true,
                'mapped' => false,
            ])
            ->add('inspector', EntityType::class, [
                // Dropdown from User table, volunteer = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(User::CATEGORY_VOLUNTEER);
                },
                'choice_label' => 'name',
                'label' => 'Visiteur·se :',
                'placeholder' => '-',
            ])
            ->add('address', EntityType::class, [
                // Dropdown from Address table
                'class' => Address::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var AddressRepository $er */
                    return $er->qbBuildings();
                },
                'choice_label' => 'address',
                'label' => 'Adresse de l\'immeuble :',
                'placeholder' => false
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => BuildingInspectionItemType::class,
                'entry_options' => ['label' => false],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuildingInspection::class,
        ]);
    }
}
