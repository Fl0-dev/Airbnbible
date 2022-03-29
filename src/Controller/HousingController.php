<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Form\HousingType;
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

    #[Route('/newHousing', name: 'newHousing')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //création d'un post
        $housing = new Housing();

        //hydratation de ce qui n'est pas géré

        $housing->setOwner($this->getUser());
        $housing->setIsVisible(true);
        $form = $this->createForm(HousingType::class, $housing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($housing);
            $entityManager->flush();
            $this->addFlash('success', 'Le logement a bien été créé');
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('housing/newHousing.html.twig', [
            'form' => $form,
        ]);
    }
}
