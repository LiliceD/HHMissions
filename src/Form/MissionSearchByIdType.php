<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Constant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * Class MissionSearchByIdType
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionSearchByIdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, [
                'constraints' => [new GreaterThan(0)],
                'required' => false
            ])
        ;
    }
}
