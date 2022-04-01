<?php

namespace App\Form;

use App\Entity\HousingCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SearchingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => HousingCategory::class,
                'choice_label' => 'name',
                'expanded' => false,
                'required' => false
            ])
            ->add('availablePlaces', IntegerType::class, [
                'label' => null,
                'required' => false
            ])
            ->add('dailyPrice', IntegerType::class, [
                'label' => null,
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => null,
                'required' => false
            ])
            ->add('entryDate', DateType::class, [
                'label' => null,
                'required' => false,
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('exitDate', DateType::class, [
                'label' => null,
                'required' => false,
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
            ])
            ->add('submit', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
