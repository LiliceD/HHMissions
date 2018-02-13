<?php

namespace App\Form;

use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('gla', null, ['label' => 'GLA :'])
			->add('address', null, ['label' => 'Adresse :'])
			->add('description', TextareaType::class, ['label' => 'Description mission :'])
			->add('info', TextareaType::class, ['label' => 'Informations supplémentaires :', 'required' => false])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults([
	        'data_class' => Mission::class,
	    ]);
	}
}