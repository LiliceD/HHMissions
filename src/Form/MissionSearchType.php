<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Constant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * Class MissionSearchType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gla', EntityType::class, [
                // Multiple choice dropdown from User table, gla = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbAllByCategory(Constant::CAT_GLA);
                },
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('volunteer', EntityType::class, [
                // Multiple choice dropdown from User table, volunteer = true
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var UserRepository $er */
                    return $er->qbAllByCategory(Constant::CAT_VOLUNTEER);
                },
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('address', EntityType::class, [
                // Dropdown from Address table
                'class' => Address::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.street', 'ASC');
                },
                'choice_label' => 'address',
                'required' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('description', null, [
                'required' => false
            ])
            ->add('dateCreatedMin', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('dateCreatedMax', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('dateFinishedMin', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
            ->add('dateFinishedMax', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'required' => false
            ])
        ;
    }
}
