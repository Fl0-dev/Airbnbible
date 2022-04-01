<?php

namespace App\Controller;

use App\Entity\Housing;
use App\Form\HousingType;
use App\Repository\HousingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/housing')]
class HousingController extends AbstractController
{
    #[Route('/', name: 'housing')]
    public function index(HousingRepository $repo): Response
    {
        $housings = $repo->findAll();

        return $this->render('housing/index.html.twig', [
            'housings' => $housings
        ]);
    }

    #[Route('/detail/{id}', name: 'housingDetails')]
    public function detail(HousingRepository $repo, $id): Response
    {
        $housing = $repo->find($id);

        return $this->render('housing/detail.html.twig', [
            'housing' => $housing
        ]);
    }

    #[Route('/add', name: 'housing_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $housing = new Housing;

        $form = $this->createForm(HousingType::class, $housing)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $housing = $form->getData();

            $city = json_decode($form->get("city")->getData());

            $housing->setOwner($this->getUser())
                    ->setIsVisible(true)
                    ->setCity($city[0])
                    ->setLatitude($city[1])
                    ->setLongitude($city[2]);

            $manager->persist($housing);

            $manager->flush();

//            $this->addFlash(
//                'success',
//                'L\'album ' . $housing->getName() . ' de ' . $housing->getArtist()->getName() . ' a bien été mis à jour !'
//            );

            return $this->redirectToRoute('housingDetails', [
                'id'    => $housing->getId(),
            ]);
        }

        return $this->renderForm('housing/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'housing_update')]
    #[IsGranted('ROLE_USER')]
    public function update(Request $request, EntityManagerInterface $manager, HousingRepository $repo, $id): Response
    {
        $housing = $repo->find($id);

        $update_form = $this->createForm(HousingType::class, $housing)->handleRequest($request);

        if ($update_form->isSubmitted() && $update_form->isValid()){
            $housing = $update_form->getData();

//            $housing->setOwner($this->getUser())
//                ->setIsVisible(true);

            $manager->persist($housing);

            $manager->flush();

//            $this->addFlash(
//                'success',
//                'L\'album ' . $housing->getName() . ' de ' . $housing->getArtist()->getName() . ' a bien été mis à jour !'
//            );

            return $this->redirectToRoute('housingDetails', [
                'id'    => $housing->getId(),
            ]);
        }

        return $this->renderForm('housing/add.html.twig', [
            'form' => $update_form,
        ]);
    }

    #[Route('/noVisible/{id}', name: 'housing_noVisible')]
    #[IsGranted('ROLE_USER')]
    public function noVisible(Request $request, EntityManagerInterface $manager, HousingRepository $repo, $id): Response
    {
        $housing = $repo->find($id);

        $housing->setIsVisible(!$housing->getIsVisible());

        $manager->persist($housing);

        $manager->flush();

        return $this->redirectToRoute('housing');
    }

    #[Route('/delete/{id}', name: 'housing_noVisible')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, EntityManagerInterface $manager, HousingRepository $repo, $id): Response
    {
        $housing = $repo->find($id);

        $repo->remove($housing);

        $manager->persist($housing);

        $manager->flush();

        return $this->redirectToRoute('housing');
    }
}
