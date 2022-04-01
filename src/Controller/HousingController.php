<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Form\HousingType;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HousingController extends AbstractController
{
    #[Route('/housing', name: 'app_housing')]
    public function index(): Response
    {
        return $this->render('housing/index.html.twig', [
            'controller_name' => 'HousingController',
        ]);
    }

    #[Route('/new/housing', name: 'newHousing')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //création d'un logement
        $housing = new Housing();

        //hydratation de ce qui n'est pas géré
        $housing->setOwner($this->getUser());
        $housing->setIsVisible(true);
        $housing->setCreatedAt(new \DateTime());
        $housing->setModifiedAt(new \DateTime());
        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //récupération des data dans city
            $city = json_decode($form->get("city")->getData());
//            dd($city);
            $housing->setCity($city[0])
                ->setLatitute($city[1])
                ->setLongitude($city[2]);

            //$housing->setAvailablePlaces($housing->avaibalePlaceMax());
            $entityManager->persist($housing);
            $entityManager->flush();
            $this->addFlash('success', 'Le logement a bien été créé');
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('housing/newHousing.html.twig', [
            'form' => $form,
        ]);

    }

    #[Route('/edit/housing/{id<\d+>}', name: 'editHousing')]
    public function editHousing(Housing $housing, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $housing->setModifiedAt(new \DateTime());
            $entityManager->flush();
            $this->addFlash('success', 'Le logement a bien été modifié.');
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('housing/newHousing.html.twig', [
            'house' => $housing,
            'form' => $form,
        ]);
    }

    #[Route('/delete/housing/{id<\d+>}', name: 'deleteHousing')]
    public function deleteHousing(Housing $housing, EntityManagerInterface $entityManager): Response
    {
        if($housing->getBookings()->isEmpty()) {
            $entityManager->remove($housing);
        } else {
            $housing->setIsVisible(false);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Le logement a bien été supprimé.');
        return $this->redirectToRoute('home');
    }
}
