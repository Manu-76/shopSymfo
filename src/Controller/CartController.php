<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ItemsInCart;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ItemsInCartRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/panier')]
class CartController extends AbstractController
{
    #[Route('/{priceId}/ajout-d-un-produit', name: 'app_cart', methods: ['POST'])]
    public function index(int $priceId, Request $request, PriceRepository $priceRepository, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $size = $data['size'];
        $quantity = $data['quantity'];

        // $priceChoosen = $priceRepository->find($priceId);
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

        // Vérifier si l'article avec la même taille existe déjà dans le panier
        $existingCartItem = $itemsInCartRepository->findOneBy([
            'price' => $priceChoosen,
            'user' => $user,
            'sizeSelected' => $size
        ]);

        if($existingCartItem) {
            // Si l'article sélectionné par le client existe déja dans la bdd avec la même taille, on met à jour la quantité
            $existingTotalItem = $existingCartItem->getTotalPriceItem();

            $existingCartItem->setQuantity($quantity);
            $existingCartItem->setTotalPriceItem($priceChoosen->getAmount() * $quantity);

            $user->setTotalInCart($user->getTotalInCart() - $existingTotalItem + ($priceChoosen->getAmount() * $quantity));

            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => 'La quantité à été mise à jour']);
        } else {
            $itemInCart = new ItemsInCart();
            $itemInCart->setPrice($priceChoosen);
            $itemInCart->setQuantity($quantity);
            $itemInCart->setSizeSelected($size);
            $itemInCart->setUser($user);
            $itemInCart->setTotalPriceItem($quantity * $priceChoosen->getAmount());

            $user->setTotalInCart($user->getTotalInCart() + $itemInCart->getTotalPriceItem());

            $entityManager->persist($itemInCart);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Le produit a bien été ajouté au panier!']);
        }
    }

    #[Route('/moins/{itemincart}/product', name: 'app_minus_product')]
    public function minus(int $itemincart, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $itemToMinus = $itemsInCartRepository->find($itemincart);
        // dump($itemToMinus);

        $itemToMinus->setQuantity($itemToMinus->getQuantity() - 1);
        // dump($itemToMinus);

        if($itemToMinus->getQuantity() === 0) {
            // dump('on est dans le if');
            $entityManager->remove($itemToMinus);
            $user->setTotalInCart($user->getTotalInCart() - $itemToMinus->getPrice()->getAmount());
            $this->addFlash('success', 'Article supprimé!');
        } else {
            // dump('on est dans le else');
            $itemToMinus->setTotalPriceItem($itemToMinus->getQuantity() * $itemToMinus->getPrice()->getAmount());
            // dd($itemToMinus);
            $user->setTotalInCart($user->getTotalInCart() - $itemToMinus->getPrice()->getAmount());
            $this->addFlash('success', 'Quantité de l\'article mise à jour!');
        }

        $entityManager->flush();
        return $this->redirectToRoute('app_user_cart');
    }
}
