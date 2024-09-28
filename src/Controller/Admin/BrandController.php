<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Un controller est un gestionnaire de routes (il peut y en avoir plusieurs), qui peut à la fois recevoir une demande d'un navigateur(requete HTTP), traiter la demande, interroger le serveur (qui lui peut interroger une base de données, recevoir les données et les renvoyer au controller), traiter les données d'un formulaire, renvoyer une vue (un template) au navigateur.
// Ici notre controller s'appelle BrandController et c'est lui traitera tout ce qui concerne les marques (en back-office)
#[Route('/admin/brand')]
final class BrandController extends AbstractController
{
    // Ici cette route n'a pas de référence d'url car elle correspond à /admin/brand, le name permet d'indiquer l'url de cette route dans un fichier twig ( ex: {{ path('app_admin_brand_index) }} )
    #[Route('/', name: 'app_admin_brand_index', methods: ['GET'])]
    // Nom de la méthode (index = souvent une liste)
    // On peut dans symfony injecter des dépendances dans la méthode, c'est-à-dire donner à la méthode un accès à des méthodes qui sont contenues dans d'autres fichiers
    public function index(BrandRepository $brandRepository): Response  //Ici on injecte le repository de Brand dans une variable ($brandRepository) et ainsi notre méthode index à accès a des méthodes propres à BrandRepository -> Principalement : findAll(), findBy(), findOneBy()et find() -> voir ex dans le repo)
    {
        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_brand_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // 1. Ici On crée une variable qui instancie un nouvel objet Marque, soit new Brand()
        $brand = new Brand();
        // 2. Ici dans la variable form, on crée notre formulaire par l'action du BrandType (dedans il y a le constructeur de formulaire, basé sur le modele d'objet $brand (pour récuperer les propriétés d'un objet brand (name....)))
        $form = $this->createForm(BrandType::class, $brand);
        // 3. Ici on dit au formulaire de gérer la requete c'est a dire de recuperer les données entrées par l'admin dans le form
        $form->handleRequest($request);

        // 5. Cette partie de code ne s'effectue que lorsque le formulaire est soumis et validé (en gros quand le bouton save est cliqué et que formulaire est controlé par le controller)
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($brand);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        // 4. Une fois 1,2,3 fait le controller renvoi au navigateur la vue (le template new.html.twig contenu dans templates/admin/brand/) avec la variable $brand (actuellement c'est un objet vide qui vient seulement d'être créé) et le formulaire (aller au Brandtype pour suite des explications)
        return $this->render('admin/brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_brand_show', methods: ['GET'])]
    public function show(Brand $brand): Response
    {
        return $this->render('admin/brand/show.html.twig', [
            'brand' => $brand,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_brand_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_brand_delete', methods: ['POST'])]
    public function delete(Request $request, Brand $brand, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($brand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_brand_index', [], Response::HTTP_SEE_OTHER);
    }
}
