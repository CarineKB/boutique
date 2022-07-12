<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProduitRepository $repo): Response
    {
        $derniersProduits= $repo->findBy([],["dateEnregistrement" => "DESC"], 5);
        return $this->render('home/index.html.twig', [
            'produits' => $derniersProduits
        ]);
    }
}
