<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Housing;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'booking')]
    public function index(BookingRepository $repo): Response
    {
        $bookings = $repo->findAll();
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/booking/add/{id}', name: 'booking')]
    public function book(Housing $housing, BookingRepository $repo, EntityManagerInterface $manager, Request $request): Response
    {
        $booking = new Booking();
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(BookingType::class, $booking)->handleRequest($request);

        $booking->setHousing($housing)
                ->setClient($user)
                ->setCreatedAt(new DateTime())
                ->setModifiedAt(new DateTime());

        if ($form->isSubmitted() && $form->isValid()){
            $interval = $booking->getEntryDate()->diff($booking->getExitDate());
            $booking->setTotalPrice($housing->getDailyPrice() * $interval->days);
            $booking = $form->getData();

            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('bookingDetails', [
                'id'    => $booking->getId(),
            ]);
        }

        return $this->renderForm('booking/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/booking/{id}', name: 'bookingDetails')]
    public function details(Booking $booking): Response
    {
        return $this->render('booking/details.html.twig', [
            'booking' => $booking,
        ]);
    }
}
