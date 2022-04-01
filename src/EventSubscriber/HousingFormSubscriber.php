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
        $form = $event->getForm();

        
        if($form->has('postalCode') && !$form->has('city')) {
            $postalCode = $event->getData()["postalCode"];
            
            $db = $this->manager->getConnection();
            $sql = '
            SELECT ville_nom FROM spec_villes_france
            WHERE ville_code_postal LIKE :code
            ';
            $stmt = $db->prepare($sql);
            $resultSet = $stmt->executeQuery(['code' => '%' . $postalCode . '%']);
    
            $cities = $resultSet->fetchAllAssociative();
            // dd($cities);
            $formatedCities = [];
            foreach($cities as $city) {
                $formatedCities[$city['ville_nom']] = $city['ville_nom'];
            } 
            // dd($formatedCities); 
            $form->add('city', ChoiceType::class, [
                'choices' => $formatedCities
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
