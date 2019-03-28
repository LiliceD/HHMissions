<?php

namespace App\Form;

use App\Entity\BuildingInspectionItemHeaders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuildingInspectionItemHeadersType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemHeadersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rank', TextType::class, [
                'label' => BuildingInspectionItemHeaders::LABEL_RANK,
                'required' => false,
                'disabled' => true,
            ])
            ->add('theme', TextareaType::class, [
                'label' => BuildingInspectionItemHeaders::LABEL_THEME,
                'required' => false,
                'disabled' => true,
            ])
            ->add('name', TextareaType::class, [
                'label' => BuildingInspectionItemHeaders::LABEL_NAME,
                'required' => false,
                'disabled' => true,
                'trim' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => BuildingInspectionItemHeaders::LABEL_DESCRIPTION,
                'required' => false,
                'disabled' => true,
                'trim' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuildingInspectionItemHeaders::class,
        ]);
    }
}
