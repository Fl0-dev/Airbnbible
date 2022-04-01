<?php

namespace App\Controller;

use App\Form\SearchHousingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function index(): Response
    {
        $housingForm = $this->createForm(SearchHousingType::class);

        if ($housingForm->isSubmitted() && $housingForm->isValid()){
//            dd($housingForm->getData());
        }

        return $this->renderForm('search/index.html.twig', [
            'form' => $housingForm,
        ]);
    }
}