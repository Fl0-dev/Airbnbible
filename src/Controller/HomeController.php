<?php

namespace App\Controller;

use App\Repository\HousingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(HousingRepository $housingRepository): Response
    {
        $allHouses = $housingRepository->findBy([],['createdAt' => 'DESC']);
        return $this->render('home/index.html.twig', [
            'houses' => $allHouses
        ]);
    }
}
