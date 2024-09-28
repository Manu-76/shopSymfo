<?php

namespace App\Controller\Admin;

use App\Entity\Shoe;
use App\Form\ShoeType;
use App\Repository\ShoeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/shoe')]
final class ShoeController extends AbstractController
{
    #[Route(name: 'app_admin_shoe_index', methods: ['GET'])]
    public function index(ShoeRepository $shoeRepository): Response
    {
        return $this->render('admin/shoe/index.html.twig', [
            'shoes' => $shoeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_shoe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // 
        $shoe = new Shoe();
        // dump($shoe);
        $form = $this->createForm(ShoeType::class, $shoe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $prices = $shoe->getPrices();
            foreach($prices as $price) {
                $shoe->addPrice($price);
            }
            // dd($prices);
            $entityManager->persist($shoe);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_shoe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/shoe/new.html.twig', [
            'shoe' => $shoe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_shoe_show', methods: ['GET'])]
    public function show(Shoe $shoe): Response
    {
        return $this->render('admin/shoe/show.html.twig', [
            'shoe' => $shoe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_shoe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Shoe $shoe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShoeType::class, $shoe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prices = $shoe->getPrices();
            foreach($prices as $price) {
                $shoe->addPrice($price);
            }
            // Ici pas besoin du persist l'objet existe déja
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_shoe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/shoe/edit.html.twig', [
            'shoe' => $shoe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_shoe_delete', methods: ['POST'])]
    public function delete(Request $request, Shoe $shoe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shoe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shoe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_shoe_index', [], Response::HTTP_SEE_OTHER);
    }
}
