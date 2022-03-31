<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Entity\Room;
use App\Form\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    #[Route('/room', name: 'app_room')]
    public function index(): Response
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }

    #[Route('/room/add/{id}', name: 'addRoom')]
    public function addRoom(Housing $housing, Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);
        $room->setHousing($housing);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($room->getBedRooms() as $bedroom){
                $bedroom->setRoom($room);
                $entityManager->persist($bedroom);
            }
            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'La chambre a bien été ajouté');
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('room/newRoom.html.twig', [
            'form' => $form,
        ]);
    }


    public function deleteRoom(Room $room, EntityManagerInterface $entityManager): ?Response
    {
        return null;
    }
}
