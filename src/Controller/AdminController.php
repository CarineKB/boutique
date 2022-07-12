<?php

namespace App\Controller;

use DateTime;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use App\Form\FormCategorieType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

 #[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{

#[Route('/produit', name: 'ajout_produit')]
public function ajout(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
$produit=  new Produit();
$form=$this->createForm(ProduitType::class,$produit);
$form->handleRequest($request);

if ($form->isSubmitted() and $form->isValid()){

$file= $form->get('photo')->getData();

$fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

try{
$file->move($this->getParameter('photos_produits'), $fileName);

}catch(FileException $e){

    //
}
$produit->setPhoto($fileName);

    $produit->setDateEnregistrement(new DateTime("now"));
    $manager= $doctrine->getManager();
    $manager->persist($produit);
    $manager->flush();

    return $this->redirectToRoute('admin_ajout_produit');
}

       return $this->render('admin/formProduit.html.twig', ['formProduit'=>$form-> createView()]);
        
    }


 #[Route('/gestion-produits', name: 'gestion_produits')]
 public function gestionProduits(ProduitRepository $repo)
 {
    $produits = $repo->findAll();
    return $this->render("admin/gestion-produits.html.twig", [
        'produits' => $produits
    ]);

 }

  #[Route('/details-produits-{id<\d+>}', name: 'detail_produit')]
  public function detailProduit($id, ProduitRepository $repo){
    $produit = $repo->find($id);

    return $this->render("admin/detail-produit.html.twig", [
        'produit'=>$produit
    ]);

  }

  #[Route('/update-produits-{id<\d+>}', name: 'update_produit')]
  public function updateProduit($id, ProduitRepository $repo, Request $request, SluggerInterface $slugger){
    $produit = $repo->find($id);
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

        if($form->get('photo')->getData()){

            $file= $form->get('photo')->getData();

            $fileName = $slugger->slug($produit->getTitre()) . uniqid() . '.' . $file->guessExtension();

            try{
            $file->move($this->getParameter('photos_produits'), $fileName);

            }catch(FileException $e){

    //
            }
            $produit->setPhoto($fileName);
        }

        $repo->add($produit,1);
        return $this->redirectToRoute("admin_gestion_produits");
    }

    return $this->render("admin/formProduit.html.twig", [
        'formProduit'=>$form->createView()
    ]);

  }
#[Route('/delete-produits-{id<\d+>}', name: 'delete_produit')]
public function deleteProduit($id, ProduitRepository $repo){

    $produit = $repo->find($id);
    
    $repo->remove($produit,1);

    return $this->redirectToRoute("admin_gestion_produits");

}

#[Route('/categorie-ajout', name: 'ajout_categorie')]
public function ajoutCategorie(Request $request, CategorieRepository $repo){
    $categorie= new Categorie();
    $form = $this ->createForm(FormCategorieType::class, $categorie);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

        $repo->add($categorie, 1);
        return $this->redirectToRoute('app_home');
    }

    return $this->render("admin/formCategorie.html.twig", [
        "formCategorie" => $form->createView()
    ]);



}

  
}

