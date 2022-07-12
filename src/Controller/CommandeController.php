<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Repository\CommandeDetailRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }
    #[Route('/passer-ma-commande', name: 'passer_commande')]
    public function passerCommande(SessionInterface $session, ProduitRepository $repoPro, CommandeRepository $repoCom,
     CommandeDetailRepository $repoDetail, EntityManagerInterface $manager){
       
        $commande= new Commande();
        $panier= $session->get("panier", []);
        $user= $this->getUser();

        if(!$user){
            $this->addFlash("error", "Veuillez vous connecter ou vous inscrire pour pouvoir passer commande!");
            return $this->redirectToRoute("app_login");
        }

        if(empty($panier)){

            $this->addFlash('error', 'votre panier est vide, vous ne pouvez pas passer commande!');
            return $this->redirectToRoute("produit_all");
        }

         $dataPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
           $produit = $repoPro->find($id);
           $dataPanier[] = [
            "produit"=>$produit,
            "quantite"=>$quantite,
            "sousTotal" => $produit->getPrix() * $quantite];

            $total += $produit->getPrix() * $quantite;
            //$total += $dataPanier["sousTotal"];
        }
       
      $commande->setUser($user)
                ->setDateDeCommande(new DateTime("now"))
                ->setMontant($total);


        $repoCom ->add($commande);

        foreach ($dataPanier as $key => $value) {

            $commandeDetail = new CommandeDetail();
            
            $produit = $value['produit'];
            $quantite = $value['quantite'];
            $sousTotal = $value['sousTotal'];

            $commandeDetail->setCommande($commande)
                           ->setProduit($produit)
                           ->setQuantite($quantite)
                           ->setPrix($sousTotal);

                           $repoDetail->add($commandeDetail);
        }
       
            $manager->flush();
            $session->remove("panier");

            $this->addFlash("success", "Félicitation, votre commande a bien été enregistrée");
            return $this->redirectToRoute("app_home");

    }

}
