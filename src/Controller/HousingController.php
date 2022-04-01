<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Form\HousingType;
use App\Form\SearchingType;
use App\Repository\BookingRepository;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HousingController extends AbstractController
{
    #[Route('/user/housings', name: 'user-housings')]
    #[IsGranted('ROLE_USER')]
    public function userHousings(HousingRepository $housingRepository): Response
    {
        $housings = [];
        /** @var User $user */
        $user = $this->getUser();
        if($user) {

            $userId = $user->getId();
    
            $housings = $housingRepository->findBy(['owner' => $userId, 'isDeleted' => false]);
        }

        return $this->render('housing/list.html.twig', [
            'housings' => $housings,
        ]);
    }

    #[Route('/housing-visibility/{id}', name: 'housing-visibility')]
    #[IsGranted('ROLE_USER')]
    public function housingVisibility(Housing $housing, EntityManagerInterface $manager): Response
    {
        $housing->setIsVisible(!$housing->getIsVisible());

        $manager->persist($housing);
        $manager->flush();

        return $this->redirectToRoute('user-housings');
    }

    #[Route('/add-housing', name: 'add-housing')]
    #[IsGranted('ROLE_USER')]
    public function addingHousing(Request $request, EntityManagerInterface $manager): Response
    {

        $housing = new Housing();

        $user = $this->getUser();

        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $city = json_decode($form->get("city")->getData());
            $housing->setModifiedAt(new \Datetime())
                    ->setCreatedAt(new \Datetime())
                    ->setOwner($user)
                    ->setIsVisible(false)
                    ->setIsDeleted(false)
                    ->setCity($city[0])
                    ->setLatitude($city[1])
                    ->setLongitude($city[2]);
            // $post = $form->getData();
            $manager->persist($housing); //prépare le sql
            $manager->flush(); //exécute le sql, indispensable!!

            return $this->redirectToRoute('user-housings');
        }   
        return $this->renderForm('housing/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/updating-housing/{id}', name: 'updating-housing')]
    #[IsGranted('ROLE_USER')]
    public function updatingHousing(Request $request, EntityManagerInterface $manager, Housing $housing, HousingRepository $housingRepository): Response
    {

        // $housing = $housingRepository->find($housing->getId());
        $user = $this->getUser();

        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $post = $form->getData();
            $housing->setModifiedAt(new \Datetime());
            $manager->persist($housing); //prépare le sql
            $manager->flush(); //exécute le sql, indispensable!!

            return $this->redirectToRoute('user-housings');
        }   
        return $this->renderForm('housing/updatingHousing.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/delete/{id}', name: 'delete-housing')]
    #[IsGranted('ROLE_USER')]
    public function index(Housing $housing, EntityManagerInterface $manager, BookingRepository $bookingRepository): Response
    {
        // dd($housing->getBookings()->isEmpty());
        if($housing->getBookings()->isEmpty()) {
            $manager->remove($housing);
        } else {
            $housing->setIsVisible(false);
            $housing->setIsDeleted(true);
           
            $bookings = $bookingRepository->findBy(['housing' => $housing]);
            
            // $bookings = $housing->getBookings();
            foreach($bookings as $booking) {
                $manager->remove($booking);
            }
            $manager->persist($housing); //prépare le sql
        }
        $manager->flush(); //exécute le sql, indispensable!!

        return $this->redirectToRoute('user-housings');
       
    }

}
