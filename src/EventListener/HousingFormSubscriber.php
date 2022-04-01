<?php

namespace App\EventListener;

use App\Entity\Equipment;
use App\Entity\HousingCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class HousingFormSubscriber implements EventSubscriberInterface
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    // evenement PRESUBMIT
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmitData',
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }

    public function preSubmitData(FormEvent $event)
    {
        $citiesTab = [];
        $form = $event->getForm();

        if ($form->has("postalCode") && !$form->has("city")) {
            $postalCode = $event->getData()['postalCode'];

            $conn = $this->manager->getConnection();
            $sql = "
SELECT ville_nom, ville_latitude_deg, ville_longitude_deg  FROM spec_villes_france_free
WHERE ville_code_postal LIKE :code";

            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['code' => "%" . $postalCode . "%"]);
            $cities = $resultSet->fetchAllAssociative();

            foreach ($cities as $line) {
                $citiesTab[$line['ville_nom']] = json_encode([
                    $line['ville_nom'],
                    $line["ville_latitude_deg"],
                    $line["ville_longitude_deg"],
                ]);
            }

            $form->add('city', ChoiceType::class, [
                'choices' => $citiesTab,
            ]);
        }
    }

    public function preSetData(FormEvent $event)
    {
        $housing = $event->getData();
        $form = $event->getForm();

        if (!$housing->getId()) {
            $form->add('adress', TextType::class)
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
                ]);
        }
    }
}