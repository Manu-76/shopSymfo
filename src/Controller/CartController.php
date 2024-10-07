<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ItemsInCart;
use App\Repository\ItemsInCartRepository;
use App\Repository\PriceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ItemsInCartRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/panier')]
class CartController extends AbstractController
{
    // #[Route('/{priceId}/ajout-d-un-produit', name: 'app_cart', methods: ['POST'])]
    // public function index(int $priceId, Request $request, PriceRepository $priceRepository, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $size = $data['size'];
    //     $quantity = $data['quantity'];

    //     // $priceChoosen = $priceRepository->findId($priceId);
    //     $priceChoosen = $priceRepository->findOneBy(['id' => $priceId]);

    //     if(!$priceChoosen) {
    //         return new JsonResponse(['success' => false, 'message' => 'L\'article sélectionné n\'existe pas!']);
    //     }

    //     $existingSizes = $priceChoosen->getSize();

    //     if(!in_array($size, $existingSizes)) {
    //         return new JsonResponse(['success' => false, 'message' => 'Taille indisponible']);
    //     }

    //     /**
    //      * @var User $user
    //      */
    //     $user = $this->getUser();

    //     // Vérifier si l'article avec la même taille existe déjà dans le panier
    //     $existingCartItem = $itemsInCartRepository->findOneBy([
    //         'price' => $priceChoosen,
    //         'user' => $user
    //     ]);

    //     if ($existingCartItem) {
    //         // Si l'article existe déjà, on met à jour la quantité et le prix total
    //         $existingCartItem->setQuantity($quantity);
    //         $existingCartItem->setTotalPriceItem($priceChoosen->getAmount() * $quantity);

    //         // Mettre à jour le total du panier de l'utilisateur
    //         $user->setTotalInCart($user->getTotalInCart() + ($priceChoosen->getAmount() * $quantity));

    //         $entityManager->flush();

    //         return new JsonResponse(['success' => true, 'message' => 'La quantité de l\'article a été mise à jour']);
    //     } else {
    //         $itemInCart = new ItemsInCart();
    //         $itemInCart->setPrice($priceChoosen);
    //         $itemInCart->setQuantity($quantity);
    //         $itemInCart->setSizeSelected($size);
    //         $itemInCart->setUser($user);
    //         $itemInCart->setTotalPriceItem($quantity * $priceChoosen->getAmount());

    //         $user->setTotalInCart($user->getTotalInCart() + $itemInCart->getTotalPriceItem());

    //         $entityManager->persist($itemInCart);
    //         $entityManager->flush();

    //         return new JsonResponse(['success' => true, 'message' => 'Article ajouté au panier!']);
    //     }
    // }

    #[Route('/{item}/ajout-d-un-produit', name:'app_add_to_cart', methods: ['POST'])]
    public function addInCart(int $item, Request $request, PriceRepository $priceRepository, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les valeurs envoyées par le formulaire
        $size = $request->request->get('size');
        $quantity = (int) $request->request->get('quantity');

        // Trouver le prix sélectionné par l'ID de l'article
        $priceChoosen = $priceRepository->find($item);

        if (!$priceChoosen) {
            $this->addFlash('danger', 'L\'article sélectionné n\'existe pas!');
            return $this->redirectToRoute('app_homepage');
        }

        // Vérifier si la taille existe pour cet article
        $existingSizes = $priceChoosen->getSize();
        if (!in_array($size, $existingSizes)) {
            $this->addFlash('danger', 'L\'article sélectionné n\'est pas disponible dans la taille choisie!');
            return $this->redirectToRoute('app_homepage');
        }

        // Récupérer l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'article avec la même taille existe déjà dans le panier
        $existingCartItem = $itemsInCartRepository->findOneBy([
            'price' => $priceChoosen,
            'user' => $user
        ]);

        if ($existingCartItem) {
            // Si l'article existe déjà, on met à jour la quantité et le prix total
            $existingCartItem->setQuantity($quantity);
            $existingCartItem->setTotalPriceItem($priceChoosen->getAmount() * $quantity);

            // Mettre à jour le total du panier de l'utilisateur
            $user->setTotalInCart($user->getTotalInCart() + ($priceChoosen->getAmount() * $quantity));

            $entityManager->flush();
            $this->addFlash('success', 'La quantité a été mise à jour dans votre panier!');
        } else {
            // Créer un nouvel article dans le panier
            $itemsInCart = new ItemsInCart();
            $itemsInCart->setPrice($priceChoosen);
            $itemsInCart->setQuantity($quantity);
            $itemsInCart->setSizeSelected($size);
            $itemsInCart->setUser($user);
            $itemsInCart->setTotalPriceItem($priceChoosen->getAmount() * $quantity);

            // Mettre à jour le total du panier de l'utilisateur
            $user->setTotalInCart($user->getTotalInCart() + $itemsInCart->getTotalPriceItem());

            // Sauvegarder dans la base de données
            $entityManager->persist($itemsInCart);
            $entityManager->flush();

            // Ajouter un message de succès
            $this->addFlash('success', 'L\'article a bien été ajouté à votre panier!');
        }

        // Redirection après l'ajout au panier
        return $this->redirectToRoute('app_homepage');
    }

    #[Route( '/moins/{item}/produit', name: 'app_minus_product', methods: ['GET'])]
    public function minus(int $item, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $itemMinus = $itemsInCartRepository->find($item);
        $itemMinus->setQuantity($itemMinus->getQuantity() - 1);

        if($itemMinus->getQuantity() === 0) {
            // dd($itemMinus);
            $entityManager->remove($itemMinus);
            $user->setTotalInCart($user->getTotalInCart() - $itemMinus->getPrice()->getAmount());
            $this->addFlash('success', 'Article supprimé!');
        } else {
            $itemMinus->setTotalPriceItem($itemMinus->getQuantity() * $itemMinus->getPrice()->getAmount());
            $user->setTotalInCart($user->getTotalInCart() - $itemMinus->getPrice()->getAmount());
            // dd($itemMinus, $user);
            $this->addFlash('success', 'Quantité de l\'article mis à jour!');
        }

        $entityManager->flush();
        return $this->redirectToRoute('app_user_account');
    }

    #[Route('/{item}/plus', name: 'app_plus_product', methods: ['GET'])]
    public function plus(int $item, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager) : Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $itemPlus = $itemsInCartRepository->find($item);
        $itemPlus->setQuantity($itemPlus->getQuantity() + 1)->setTotalPriceItem($itemPlus->getQuantity() * $itemPlus->getPrice()->getAmount());
        $user->setTotalInCart($user->getTotalInCart() + $itemPlus->getPrice()->getAmount());

        $entityManager->flush();

        $this->addFlash('success', 'Quantité de l\'article mis à jour!');

        return $this->render('user_account/index.html.twig', [
            'user' => $user,
        ]);
        // return $this->redirectToRoute('app_user_account');
    }

    #[Route('/{item}/retirer', name: 'app_remove_product', methods: ['GET'])]
    public function removeItem(int $item, ItemsInCartRepository $itemsInCartRepository, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        $objectItemInCart = $itemsInCartRepository->find($item);
        // $objectItemInCart = {
        //     'article' : 'tong',
        //     'quantity': 3,
        //     'totalPriceItem': 234,
        // }
        $totalPriceItemToRemove = $objectItemInCart->getTotalPriceItem();
        // $totalPriceItemToRemove = 234€;
        $user->setTotalInCart($user->getTotalInCart() - $totalPriceItemToRemove);
        
        $entityManager->remove($objectItemInCart);
        $entityManager->flush();

        $this->addFlash('success', 'l\'article a bien été supprimé!');
        return $this->redirectToRoute(('app_user_account'));
    }

    #[Route('/remove/all/products', name: 'app_cart_remove_all', methods: ['GET'])]
    public function emptytrash(EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        
        $user->setTotalInCart(0);
        $itemsInCart = $user->getItemsInCarts();
        foreach($itemsInCart as $itemInCart) {
            $entityManager->remove($itemInCart);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre panier a bien été vidé!');
        return $this->redirectToRoute('app_user_account');
    }
}
