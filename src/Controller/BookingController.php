<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Housing;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    #[Route('/booking/{id}', name: 'app_booking')]
    #[IsGranted('ROLE_USER')]
    public function index(Housing $housing, Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $idHousing = $housing->getId();

        $user = $this->getUser();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        $booking->setCreatedAt(new \Datetime());
        $booking->setModifiedAt(new \Datetime());
        $booking->setClient($user);
        $booking->setHousing($housing);

        
        if ($form->isSubmitted() && $form->isValid()) {
            // $post = $form->getData();
            $entryDate = $booking->getEntryDate();
            $exitDate = $booking->getExitDate();
            
            $stayTime = ($entryDate->diff($exitDate))->d;
            $booking->setTotalPrice($housing->getDailyPrice() * $stayTime);

            $manager->persist($booking); //prépare le sql
            $manager->flush(); //exécute le sql, indispensable!!

            return $this->redirectToRoute('home');
        }   
        return $this->renderForm('booking/index.html.twig', [
            'form' => $form,
            'idHousing' => $idHousing
        ]);
    }

    #[Route('/bookings', name: 'bookings-list')]
    #[IsGranted('ROLE_USER')]
    public function listBookings(BookingRepository $bookingRepository): Response
    {
        $bookings = [];
        /**
         * @var User $user 
         */
        $user = $this->getUser();
        $bookings = $bookingRepository->findBy(['client' => $user->getId()]);
        
        return $this->render('booking/list.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/getbookings/{id}', name: 'get-bookings')]
    #[IsGranted('ROLE_USER')]
    public function getBookings(Housing $housing, BookingRepository $bookingRepository): Response
    {
        $bookings = [];
        $newBookings = [];

        $bookings = $bookingRepository->findBy(['housing' => $housing]);
        
        foreach($bookings as $booking) {
            $title = 'réservé';
            $entryDate = $booking->getEntryDate()->format('Y-m-d');
            $exitDate = $booking->getExitDate()->format('Y-m-d');
            $newBookings[] = ['title' => $title, 'start' => $entryDate, 'end' => $exitDate];
        };
    
        return $this->json($newBookings);
    }
}
