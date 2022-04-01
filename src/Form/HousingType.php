<?php

namespace App\Form;

use App\Entity\Housing;
use App\Entity\HousingCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\EventSubscriber\HousingFormSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HousingType extends AbstractType
{
    private $manager;
    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('adress')
            ->add('postalCode', TextType::class, [])
            ->add('availablePlaces')
            ->add('dailyPrice')
            ->add('category', EntityType::class, [
                'class' => HousingCategory::class, 
                'choice_label' => 'name',
                'expanded' => true,
            ])
            ->add('description')
            ->add('save', SubmitType::class);
        ;
        $builder->addEventSubscriber(new HousingFormSubscriber($this->manager));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Housing::class,
        ]);
    }
}
