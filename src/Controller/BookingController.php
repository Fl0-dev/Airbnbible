<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Housing;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $reservations = $bookingRepository->findBy(['client'=> $user]);
        return $this->render('booking/index.html.twig', [
            'reservations' => $reservations
        ]);
    }

    #[Route('/new/booking/{id<\d+>}', name: 'newBooking')]
    public function addBooking(Housing $housing, Request $request, EntityManagerInterface $entityManager)
    {
        $booking = new Booking;

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
        $booking->setCreatedAt(new \DateTime());
        $booking->setModifiedAt(new \DateTime());
        $booking->setClient($this->getUser());
        $booking->setHousing($housing);

        if ($form->isSubmitted() && $form->isValid()) {
            $entryDate = $booking->getEntryDate();
            $exitDate = $booking->getExitDate();
            $journeyTime = ($entryDate->diff($exitDate))->d;
            $booking->setJourneyTime($journeyTime);
            $booking->setTotalPrice($journeyTime*$housing->getDailyPrice());
            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Votre réservation a bien été prise en compte');
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('booking/reservation.html.twig', [
            'form' => $form,
            'housingId' =>$housing->getId()
        ]);
    }

    #[Route('/getbookings/{id}', name: 'get-bookings')]
    public function calendar(Housing $housing, BookingRepository $bookingRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $bookings = $bookingRepository->findBy(['housing'=>$housing]);
        $dates = [];
        foreach($bookings as $booking) {
            $title = 'Réservé';
            $entryDate = $booking->getEntryDate()->format('Y-m-d');
            $exitDate=$booking->getEntryDate()->format('Y-m-d');
            $dates[] = ['title' => $title, 'start' => $entryDate, 'end' => $exitDate];

        }

        return $this->json($dates);
    }
}
