<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/room')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'room')]
    public function index(): Response
    {
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
    }


    #[Route('/{housing}/add', name: 'add_room')]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, EntityManagerInterface $manager, Housing $housing): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room)->handleRequest($request);
        $room->setHousing($housing);

        if ($form->isSubmitted() && $form->isValid()){
            foreach ($room->getBedRooms() as $bedRoom){
                $bedRoom->setRoom($room);
                $manager->persist($bedRoom);
            }
            $room = $form->getData();

            $manager->persist($room);
            $manager->flush();

            return $this->redirectToRoute('housingDetails', [
                'id'    => $housing->getId(),
            ]);
        }

        return $this->renderForm('room/add.html.twig', [
            'form' => $form,
            'housing' => $housing
        ]);
    }
}
