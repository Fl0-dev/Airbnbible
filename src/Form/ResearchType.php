<?php

namespace App\Form;

use App\Entity\HousingCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('entryDate', DateType::class, [
                'widget' => 'single_text',

            ])
            ->add('exitDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbGuest', IntegerType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => HousingCategory::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('localisation', TextType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
