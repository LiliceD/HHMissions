<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; // for "glaRole"
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvent; // for EventListener
use Symfony\Component\Form\FormEvents; // for EventListener

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Prénom NOM :'])
            ->add('username', TextType::class, ['label' => 'Nom d\'utilisateur :'])
            ->add('email', EmailType::class, ['label' => 'Email :'])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmer le mot de passe :'],
            ])
            // Checkbox is displayed or not / checked or not by javascripts
            ->add('glaRole', CheckboxType::class, [
                'label' => 'Autoriser ce·tte bénévole à créer des fiches mission',
                'data' => false,
                'required' => false,
                'mapped' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // Checks if the user is being created or already exists
            if (!$user || null === $user->getId()) {
                // Add "first name" and "last name" fields for user creation
                $form
                    ->add('firstName', TextType::class, [
                        'label' => 'Prénom :',
                        'mapped' => false
                    ])
                    ->add('lastName', TextType::class, [
                        'label' => 'Nom :',
                        'mapped' => false
                    ])
                ;
                // Add a "category" dropdown
                $form->add('category', ChoiceType::class, [
                    'choices' => User::getCategories(),
                    'label' => 'Catégorie :',
                    'placeholder' => '-',
                    'mapped' => false
                ]);
            } else {
                // Add a "category" dropdown prepopulated with user's category
                $userCategory = $user->getCategory(); // choice name (e.g. 'Admin'), to display to user
                $selectedCategory = User::getCategories()[$userCategory]; // choice value (e.g. 'ROLE_ADMIN'), to set preferred_choices
                
                $form->add('category', ChoiceType::class, [
                    'choices' => User::getCategories(),
                    'label' => 'Catégorie :',
                    'placeholder' => false,
                    'preferred_choices' => [$selectedCategory],
                    'mapped' => false
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}