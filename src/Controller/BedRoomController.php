<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\BedRoom;
use App\Form\BedRoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BedRoomController extends AbstractController
{
    #[Route('/{id}/add-bedroom', name: 'add-bedroom')]
    #[IsGranted('ROLE_USER')]
    public function index(Room $room, Request $request, EntityManagerInterface $manager): Response
    {

        $bedroom = new BedRoom();

        $form = $this->createForm(BedRoomType::class, $bedroom);
        $form->handleRequest($request);

        $bedroom->setRoom($room);

        if($form->isSubmitted() && $form->isValid()) {
            
            $manager->persist($bedroom); //prépare le sql
            $manager->flush(); //exécute le sql, indispensable!!

            return $this->redirectToRoute('home');
        }   
        return $this->renderForm('bed_room/new.html.twig', [
            'form' => $form,
        ]);
    }
}
