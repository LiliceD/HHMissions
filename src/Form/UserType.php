<?php

namespace App\Form;

use App\Entity\User;
use App\Utils\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class UserType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom :',
                'mapped' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom :',
                'mapped' => false
            ])
            ->add('name', TextType::class, ['label' => 'Prénom NOM :'])
            ->add('username', TextType::class, ['label' => 'Identifiant :'])
            ->add('email', EmailType::class, ['label' => 'Email :'])
            ->add('category', ChoiceType::class, [
                'choices' => Constant::getUserCategories(),
                'label' => 'Catégorie :',
                'placeholder' => '-',
            ])
            ->add('activities', ChoiceType::class, [
                'choices' => Constant::getActivities(),
                'expanded' => true,
                'multiple' => true,
                'label' => 'Pôle·s d\'activité :',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmer le mot de passe :'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
