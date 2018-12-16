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

/**
 * Class UserEditType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Prénom NOM :'])
            ->add('username', TextType::class, ['label' => 'Identifiant :'])
            ->add('email', EmailType::class, ['label' => 'Email :'])
            ->add('category', ChoiceType::class, [
                'choices' => User::getCategories(),
                'label' => 'Catégorie :',
                'placeholder' => false,
            ])
            ->add('activities', ChoiceType::class, [
                'choices' => Constant::getActivities(),
                'expanded' => true,
                'multiple' => true,
                'label' => 'Pôle·s d\'activité :',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => ['edit'] // constraints on 'name' only
        ));
    }
}
