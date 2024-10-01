<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ItemsInCart;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/panier')]
class CartController extends AbstractController
{
    #[Route('/{priceId}/ajout-d-un-produit', name: 'app_cart', methods: ['POST'])]
    public function index(int $priceId, Request $request, PriceRepository $priceRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $size = $data['size'];
        $quantity = $data['quantity'];

        // $priceChoosen = $priceRepository->findId($priceId);
        $priceChoosen = $priceRepository->findOneBy(['id' => $priceId]);

        if(!$priceChoosen) {
            return new JsonResponse(['success' => false, 'message' => 'L\'article sélectionné n\'existe pas!']);
        }

        $existingSizes = $priceChoosen->getSize();

        if(!in_array($size, $existingSizes)) {
            return new JsonResponse(['success' => false, 'message' => 'Taille indisponible']);
        }

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $itemInCart = new ItemsInCart();
        $itemInCart->setPrice($priceChoosen);
        $itemInCart->setQuantity($quantity);
        $itemInCart->setSizeSelected($size);
        $itemInCart->setUser($user);
        $itemInCart->setTotalPriceItem($quantity * $priceChoosen->getAmount());

        $user->setTotalInCart($user->getTotalInCart() + $itemInCart->getTotalPriceItem());

        $entityManager->persist($itemInCart);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
