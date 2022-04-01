<?php

namespace App\Controller;

use App\Form\SearchHousingType;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $manager, HousingRepository $housingRepo, Request $request): Response
    {
        $housings = $housingRepo->findLastHousings(4);

        $housingForm = $this->createForm(SearchHousingType::class)->handleRequest($request);

        if ($housingForm->isSubmitted() && $housingForm->isValid()){
            $data = $housingForm->getData();

            $housings = $housingRepo->findBySearch($data);
        }

        return $this->render('home/index.html.twig', [
            'housings' => $housings,
            'form' => $housingForm->createView(),
        ]);
    }
}