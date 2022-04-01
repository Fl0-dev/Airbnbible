<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Entity\BedRoom;
use App\Entity\Housing;
use App\Form\RoomType2;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RoomController extends AbstractController
{
    // #[Route('/{id}/add-room', name: 'add-room')]
    // public function addRoom(Request $request, EntityManagerInterface $manager, Housing $housing): Response
    // {
    //     $room = new Room();
    //     $form = $this->createForm(RoomType::class, $room);
    //     $form->handleRequest($request);

    //     $room->setHousing($housing);

    //     if($form->isSubmitted() && $form->isValid()) {
            
    //         $manager->persist($room); //prépare le sql
    //         $manager->flush(); //exécute le sql, indispensable!!

    //         return $this->redirectToRoute('home');
    //     }   
    //     return $this->renderForm('room/index.html.twig', [
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/room-list/{id}', name: 'room-list')]
    #[IsGranted('ROLE_USER')]
    public function roomList(Request $request, EntityManagerInterface $manager, Housing $housing, RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findBy(["housing" => $housing]);
        
        return $this->renderForm('room/list-rooms.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/details/{id}', name: 'room-details')]
    public function rooDetails(Room $room): Response
    {
        // $room = $roomRepository->find($id);
        
        return $this->renderForm('room/details.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/add-room/{id}', name: 'add-bedroom-and-room')]
    #[IsGranted('ROLE_USER')]
    public function addBedroomAndRoom(Housing $housing, Request $request, EntityManagerInterface $manager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType2::class, $room);
        $form->handleRequest($request);

        $room->setHousing($housing);

        if($form->isSubmitted() && $form->isValid()) {
            
            foreach($room->getBedRooms() as $bedroom) {
                $bedroom->setRoom($room);
                $manager->persist($bedroom);
            }
            
            $manager->persist($room); //prépare le sql
            $manager->flush(); //exécute le sql, indispensable!!

            return $this->redirectToRoute('user-housings');
        }   
        return $this->renderForm('room/index.html.twig', [
            'form' => $form,
        ]);
       
    }

    #[Route('/delete/room/{id}', name: 'delete-room')]
    #[IsGranted('ROLE_USER')]
    public function index(Room $room, EntityManagerInterface $manager): Response
    {
        // dd($housing->getBookings()->isEmpty());

        $manager->remove($room);
        $manager->flush();
       

        return $this->redirectToRoute('user-housings');
       
    }
    
}
