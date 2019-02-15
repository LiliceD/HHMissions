<?php


namespace App\Form;


use App\Entity\BuildingInspectionItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BuildingInspectionItemType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => 'Constats faits par le visiteur / Propositions d\'actions :',
                'data' => 'OK',
                'trim' => false,
                ])
            ->add('action', ChoiceType::class, [
                'choices' => BuildingInspectionItem::getActions(),
                'label' => 'Action :',
                'required' => false,
                'placeholder' => false,
            ])
            ->add('decisionMaker', ChoiceType::class, [
                'choices' => BuildingInspectionItem::getDecisionMakers(),
                'label' => 'Décisions d\'engagement  :',
                'required' => false,
                'placeholder' => false,
            ])
            ->add('headers', BuildingInspectionItemHeadersType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BuildingInspectionItem::class,
        ]);
    }
}
{

}
