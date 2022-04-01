<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchingType;
use App\Repository\UserRepository;
use App\Repository\HousingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, HousingRepository $housingRepository): Response
    {
        $housings = [];
        $housings = $housingRepository->findBy(['isDeleted' => false, 'isVisible' => true]);

        $form = $this->createForm(SearchingType::class);
        $form->handleRequest($request);

        $data = $form->getData();
        // dd(!empty($data));
        if(!empty($data)) {
            $housings = $housingRepository->findBySearch($data);
            // dd($housings);
        }

        return $this->renderForm('home/index.html.twig', [
            'form' => $form,
            'housings' => $housings,
        ]);
    }
}
