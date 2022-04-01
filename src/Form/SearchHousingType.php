<?php

namespace App\Form;

use App\Entity\HousingCategory;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHousingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entryDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('exitDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('availablePlaces', IntegerType::class, [
//                "required" => false
            ])
//            ->add('dailyPrice', IntegerType::class, [
////                "required" => false
//            ])
            ->add('category', EntityType::class, [
                "class" => HousingCategory::class,
                'choice_label' => 'name',
                'expanded' => true,
            ])
            ->add('Rechercher', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
