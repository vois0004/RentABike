<?php

namespace App\Form;


use App\Entity\Bike;
use App\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bike', EntityType::class, [
                'class' => Bike::class,
                'choice_label' => 'code',
                'query_builder' => function (EntityRepository $er) use ($builder) {
                    return $er->createQueryBuilder('b')
                        ->innerJoin('b.Station', 's', Expr\Join::WITH, 's = :station')
                        ->setParameter('station', $builder->getData()->getStationBegin());
                },
            ])
            ->add('paiementId', HiddenType::class, [
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ride::class,
        ]);
    }


}
