<?php

namespace App\Form;

use App\Entity\Accomodation;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent; // for addEventListener
use Symfony\Component\Form\FormEvents; // for addEventListener
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
            ->add('gla', null, ['label' => 'GLA :'])
            ->add('volunteer', null, ['label' => 'Bénévole :'])
            ->add('accomodation', EntityType::class, [
                'class' => Accomodation::class,
                'choice_label' => 'address',
                'label' => 'Adresse :',
                'placeholder' => false
            ])
            ->add('description', TextareaType::class, ['label' => 'Description mission :'])
            // ->add('info', TextareaType::class, [
            //     'label' => 'Informations complémentaires :',
            //     'required' => false,
            // ])
            ->add('conclusions', TextareaType::class, [
                'label' => 'Retour mission et conclusions :',
                'required' => false
            ])
        ;

        $formModifier = function (FormInterface $form, Accomodation $accomodation = null) {
            $access = null === $accomodation ? '' : $accomodation->getAccess();

            $form->add('info', TextareaType::class, [
                'data' => $access
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();

                $mission = $event->getData();
                $accomodation = $mission->getAccomodation();

                $formModifier($form, $accomodation);                
            }
        );

        $builder->get('accomodation')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) use ($formModifier) {
                $form = $event->getForm();

                $accomodation = $form->getData();
                // $accomodation = $mission->getAccomodation();

                $formModifier($form->getParent(), $accomodation);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}