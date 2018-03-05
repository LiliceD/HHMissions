<?php

namespace App\Form;

use App\Entity\Accomodation;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
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
            ->add('status', null, array('label' => 'Statut :'))
            ->add('dateCreated', DateType::class, array(
                'label' => 'Date demande :',
                'widget' => 'single_text',
            ))
            ->add('dateAssigned', DateType::class, array(
                'label' => 'Date prise en charge :',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('dateFinished', DateType::class, array(
                'label' => 'Date fin de mission :',
                'widget' => 'single_text',
                'required' => false
            ))
            ->add('gla', null, array('label' => 'GLA :'))
            ->add('volunteer', null, array('label' => 'Bénévole :'))
            ->add('accomodation', EntityType::class, array(
                'class' => Accomodation::class,
                'choice_label' => 'address',
                'label' => 'Adresse d\'intervention :',
                'placeholder' => false
            ))
            ->add('description', TextareaType::class, array('label' => 'Description mission :'))
            ->add('info', TextareaType::class, array(
                'label' => 'Informations complémentaires :',
                'required' => false,
            ))
            ->add('conclusions', TextareaType::class, array(
                'label' => 'Retour mission et conclusions :',
                'required' => false
            ))
            ->add('pdfScan', FileType::class, array(
                'label' => 'Scan PDF :',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}