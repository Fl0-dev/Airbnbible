<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Housing;
use App\Entity\HousingCategory;
use App\Form\EventListener\HousingFormSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HousingType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address')
            ->add('postalCode',TextType::class,[
                //'mapped' => false
            ])
            //->add('city')
            ->add('availablePlaces')
            ->add('dailyPrice')
            ->add('category', EntityType::class, [
                'class' => HousingCategory::class,
                'choice_label' => 'name'
            ])
            ->add('equipments', EntityType::class, [
                'class' => Equipment::class,
                'choice_label' => 'name',
                'multiple' => 'true'
            ])
            ->add('description',TextType::class)
        ;
        $builder->addEventSubscriber(new HousingFormSubscriber($this->em));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Housing::class,
        ]);
    }
}
