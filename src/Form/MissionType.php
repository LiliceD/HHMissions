<?php

namespace App\Form;

use App\Entity\Accomodation;
use App\Entity\Mission;
use App\Entity\User;
use Doctrine\ORM\EntityRepository; // for 'address' query builder
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // for 'gla' drop-down;
use Symfony\Component\Form\Extension\Core\Type\DateType; // for 'date created/assigned/finished' fields
use Symfony\Component\Form\Extension\Core\Type\FileType; // for 'Scan PDF'
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', null, ['label' => 'Statut :'])
            ->add('dateCreated', DateType::class, [
                'label' => 'Date demande :',
                'widget' => 'single_text',
            ])
            ->add('dateAssigned', DateType::class, [
                'label' => 'Date prise en charge :',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('dateFinished', DateType::class, [
                'label' => 'Date fin de mission :',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('gla', EntityType::class, [
                // Dropdown from User table, isGla = true, isActive = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->qbActiveByCategory('gla');
                },
                'choice_label' => 'name',
                'label' => 'Provenance GLA :',
                'placeholder' => false
            ])
            ->add('volunteer', EntityType::class, [
                // Dropdown from User table, isVolunteer = true, isActive = true
                'class' => User::class,
                // 'query_builder' => function (EntityRepository $er) {
                //     return $er->qbActiveByCategory('volunteer');
                // },
                'choice_label' => 'name',
                'label' => 'Bénévole :',
                'placeholder' => '-',
                'required' => false
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
                'placeholder' => false
            ])
            ->add('description', TextareaType::class, ['label' => 'Description mission :'])
            ->add('info', TextareaType::class, [
                'label' => 'Informations complémentaires :',
                'required' => false,
            ])
            ->add('conclusions', TextareaType::class, [
                'label' => 'Retour mission et conclusions :',
                'required' => false
            ])
            ->add('attachment', FileType::class, [
                'label' => 'Pièce jointe :',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}