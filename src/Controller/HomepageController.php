<?php

// Dossier racine qui contient nos controllers (App = shopSymfo/src)
namespace App\Controller;

// Utilisation par symfony de bundle ou composant
use App\Repository\ShoeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclaration de la classe HomepageController qui s'étend du AbstractController et qui permet d'utiliser des méthodes contenues dans AbstractController
class HomepageController extends AbstractController
{
    // #[Route('/homepage', name: 'app_homepage')] , La page d'accueil d'un site web n'affiche jamais /homepage, on peut donc le supprimer
    #[Route('/', name: 'app_homepage')]
    // Méthode(s) utilisée(s) dans ce controller 
    public function index(ShoeRepository $shoeRepository): Response
    {
        // Action attendue du controller, c'est a dire renvoyer la vue du template au navigateur, avec entre [] la possibilité d'ajout d'option, tel que déclarer une variable et injecter sa valeur dans le template
        return $this->render('homepage/index.html.twig', [
            'shoes' => $shoeRepository->findAll(),
        ]);
    }
}
