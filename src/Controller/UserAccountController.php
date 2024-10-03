<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]
class UserAccountController extends AbstractController
{
    #[Route('/user/account', name: 'app_user_account')]
    public function index(): Response
    {
        return $this->render('user_account/index.html.twig', [
            'controller_name' => 'UserAccountController',
        ]);
    }

    #[Route('/panier', name: 'app_user_cart')]
    public function cartIndex(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        
        return $this->render('user_account/mycart.html.twig', [
            'user' => $user,
        ]);
    }
}
