<?php

namespace App\Controller;

use App\Form\ResearchType;
use App\Repository\HousingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(HousingRepository $housingRepository, Request $request): Response
    {
        $allHouses = $housingRepository->findBy(['isVisible' => true],['createdAt' => 'DESC']);
        $form = $this->createForm(ResearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData = $form->getData();
            $allHouses = $housingRepository->findBySearch($searchData);
        }

        return $this->renderForm('home/index.html.twig', [
            'form' => $form,
            'houses' => $allHouses
        ]);
    }
}
