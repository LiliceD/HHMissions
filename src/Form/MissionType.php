<?php

namespace App\Form;

use App\Entity\Accomodation;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // for 'address' drop-down
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
				'label' => 'Adresse :'
			])
			->add('description', TextareaType::class, ['label' => 'Description mission :'])
			->add('info', TextareaType::class, [
				'label' => 'Informations complémentaires :',
				'required' => false
			])
			->add('conclusions', TextareaType::class, [
				'label' => 'Retour mission et conclusions :',
				'required' => false
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults([
	        'data_class' => Mission::class,
	    ]);
	}
}