<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            FormEvents::PRE_SUBMIT => 'preSubmitData'
        ];
    }

    public function preSubmitData(FormEvent $event){
        $citiesName = [];
//        $housing = $event->getData();
        $form = $event->getForm();

        if ($form->has("postalCode") && !$form->has("city")){
            $postalCode = $event->getData()['postalCode'];

            $conn = $this->manager->getConnection();
            $sql = "
SELECT ville_nom FROM spec_villes_france_free
WHERE ville_code_postal LIKE :code";

            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery(['code' => "%" . $postalCode . "%"]);
            $cities = $resultSet->fetchAllAssociative();

            foreach ($cities as $line){
                $citiesName[$line['ville_nom']] = $line['ville_nom'];
            }

            $form->add('city', ChoiceType::class, [
                'choices' => $citiesName,
            ]);
        }
    }
}