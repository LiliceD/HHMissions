<?php

namespace App\Form;

use App\Entity\User;
use App\Utils\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // for 'category'
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent; // for EventListener
use Symfony\Component\Form\FormEvents; // for EventListener

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Prénom NOM :'])
            ->add('username', TextType::class, ['label' => 'Identifiant :'])
            ->add('email', EmailType::class, ['label' => 'Email :'])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // Add a "category" dropdown prepopulated with user's category
            $user = $event->getData();
            $form = $event->getForm();

            // Retrieve user's category
            // choice name (e.g. 'Admin'), to display to user
            $userCategory = $user->getCategory();
            // choice value (e.g. 'ROLE_ADMIN'), to set preferred_choices
            $selectedCategory = Constant::getUserCategoriesDropdown()[$userCategory];
            
            // Add field
            $form->add('category', ChoiceType::class, [
                'choices' => Constant::getUserCategoriesDropdown(),
                'label' => 'Catégorie :',
                'placeholder' => false,
                'preferred_choices' => [$selectedCategory],
                'mapped' => false
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => ['edit'] // constraints on 'name' only
        ));
    }
}
