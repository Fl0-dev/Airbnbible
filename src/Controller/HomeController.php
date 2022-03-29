<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $conn = $em->getConnection();
        $sql = '
        SELECT ville_nom FROM spec_villes_france_free
        WHERE ville_code_postal = :code
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['code' => "46100"]);

        $cities = $resultSet->fetchAllAssociative();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
