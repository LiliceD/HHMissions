<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // for "glaRole"
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
            // Checkbox is displayed or not / checked or not by javascripts
            ->add('glaRole', CheckboxType::class, [
                'label' => 'Autoriser ce·tte bénévole à créer des fiches mission',
                'data' => false,
                'required' => false,
                'mapped' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // Add a "category" dropdown prepopulated with user's category
            $user = $event->getData();
            $form = $event->getForm();

            // Retrieve user's category
            $userCategory = $user->getCategory(); // choice name (e.g. 'Admin'), to display to user
            $selectedCategory = User::getCategories()[$userCategory]; // choice value (e.g. 'ROLE_ADMIN'), to set preferred_choices
            
            // Add field
            $form->add('category', ChoiceType::class, [
                'choices' => User::getCategories(),
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