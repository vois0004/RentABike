<?php

namespace App\Form;

use App\Entity\Ride;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Duration')
            ->add('bike')
            ->add('User')
            ->add('stationBegin')
            ->add('StationEnd')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
        ]);
    }
}
