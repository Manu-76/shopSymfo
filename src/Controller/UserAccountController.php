<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]
class UserAccountController extends AbstractController
{
    #[Route('/panier', name: 'app_user_account')]
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        return $this->render('user_account/index.html.twig', [
            'user' => $user,
        ]);
    }
}
