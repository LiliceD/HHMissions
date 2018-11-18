<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Mission;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Constant;
use Doctrine\ORM\EntityRepository; // for 'address' query builder
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // for 'date created/assigned/finished' fields
use Symfony\Component\Form\Extension\Core\Type\FileType; // for 'attachment'
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MissionType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activity', ChoiceType::class, [
                'choices' => Constant::getActivities(),
                'label' => 'Pôle d\'activité :',
                'placeholder' => '-- Pôle d\'activité -- ',
            ])
            ->add('status', null, ['label' => 'Statut :'])
            ->add('dateCreated', DateType::class, [
                'label' => 'Date de demande :',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy'
            ])
            ->add('dateAssigned', DateType::class, [
                'label' => 'Date de prise en charge :',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('dateFinished', DateType::class, [
                'label' => 'Date de fin de mission :',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('gla', EntityType::class, [
                // Dropdown from User table, gla = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(Constant::CAT_GLA);
                },
                'choice_label' => 'name',
                'label' => 'Provenance GLA :',
                'placeholder' => false
            ])
            ->add('volunteer', EntityType::class, [
                // Dropdown from User table, volunteer = true, active = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbActiveByCategory(Constant::CAT_VOLUNTEER);
                },
                'choice_label' => 'name',
                'label' => 'Bénévole :',
                'placeholder' => '-',
                'required' => false,
            ])
            ->add('address', EntityType::class, [
                // Dropdown from Address table
                'class' => Address::class,
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
