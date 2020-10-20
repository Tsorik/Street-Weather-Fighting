<?php

namespace App\Controller;

use App\Repository\VillesFranceFreeRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function index($slug, VillesFranceFreeRepository $villesFranceFree)
    {
        $data_map = $villesFranceFree->findOneBy(['villeNomSimple' => $slug]);
        
        return $this->render('details/index.html.twig', [
            'city_name' => $slug,
            'lng' => $data_map->villeLongitudeDeg,
            'lat' => $data_map->villeLatitudeDeg
        ]);
    }
}
