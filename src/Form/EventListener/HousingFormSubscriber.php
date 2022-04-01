<?php

namespace App\Form\EventListener;

use App\Entity\Equipment;
use App\Entity\Housing;
use App\Entity\HousingCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class HousingFormSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmitData',
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }


    public function preSetData(FormEvent $event)
    {
        $housing = $event->getData();
        $form = $event->getForm();

        if($housing->getId() === null) {
            $form->add('dailyPrice')
                ->add('address')
                ->add('postalCode',TextType::class,[
                    //'mapped' => false
                ])
                ->add('category', EntityType::class, [
                    'class' => HousingCategory::class,
                    'choice_label' => 'name'
                ])
                ->add('availablePlaces')
                ->add('equipments', EntityType::class, [
                    'class' => Equipment::class,
                    'choice_label' => 'name',
                    'multiple' => 'true'
                ]);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function preSubmitData(FormEvent $event)
    {

        $form = $event->getForm();

        if ($form->has('postalCode') && !$form->has('city')) {

            $postalCode = $event->getData()["postalCode"];

            $conn = $this->em->getConnection();
            $sql = '
            SELECT ville_nom FROM spec_villes_france_free
            WHERE ville_code_postal Like :code';
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['code' => "%". $postalCode ."%"]);

            $cities = $resultSet->fetchAllAssociative();
            $citiesName = [];
            foreach ($cities as $city) {
                $citiesName[$city['ville_nom']] = $city['ville_nom'];
            }

            $form->add('city',ChoiceType::class, [
                'choices' => $citiesName,
            ]);


        }
    }
}