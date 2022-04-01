<?php

namespace App\EventSubscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class HousingFormSubscriber implements EventSubscriberInterface
{
    private $manager;
    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }
    
    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            FormEvents::PRE_SUBMIT => ['onPreSubmitting'],
            FormEvents::PRE_SET_DATA => ['onPreSetting'],
        ];
    }

    public function onPreSubmitting(FormEvent $event) {
        $citiesTab = [];
        $form = $event->getForm();

        if ($form->has("postalCode") && !$form->has("city")) {
            $postalCode = $event->getData()['postalCode'];

            $conn = $this->manager->getConnection();
            $sql = "
            SELECT ville_nom, ville_latitude_deg, ville_longitude_deg  FROM spec_villes_france
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

    public function onPreSetting(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

        if($data->getId()) {
            $form->remove('adress')
                ->remove('postalCode')
                ->remove('category')
                ->remove('availablePlaces')
                ->remove('dailyPrice');
        }
    }
}
