<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Housing;
use App\Entity\HousingCategory;
use App\EventListener\HousingFormSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HousingType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adress', TextType::class)
            ->add('postalCode', IntegerType::class)
            ->add('availablePlaces', IntegerType::class)
            ->add('dailyPrice', IntegerType::class)
            ->add('category', EntityType::class, [
                'class' => HousingCategory::class,
                'choice_label' => 'name',
                'expanded' => true,
            ])
            ->add('equipments', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->addEventSubscriber(new HousingFormSubscriber($this->manager));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Housing::class,
        ]);
    }
}
