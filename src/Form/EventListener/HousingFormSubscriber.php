<?php

namespace App\Form\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
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
            FormEvents::PRE_SUBMIT => 'preSubmitData'
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function preSubmitData(FormEvent $event)
    {

        $form = $event->getForm();
        $postalCode = $event->getData()["postalCode"];

        if ($form->has('postalCode') && !$form->has('city')) {

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