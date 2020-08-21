<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class VersusController extends AbstractController
{
    /**
     * @Route("/versus", name="versus")
     */
    public function index(Request $request)
    {
        $id1 = $request->get('id1');
        $id2 = $request->get('id2');

        $insee = file_get_contents('https://api.meteo-concept.com/api/location/cities?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&search='. $id1 . '');

        if ($insee !== false) {
            $table_cities = json_decode($insee);
            $cities = $table_cities->{'cities'};
            $cities = $cities[0];
            $insee_num = $cities->{'insee'};
        }

        $data = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/0?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&insee=' . $insee_num . '');


        if ($data !== false) {
            $decoded = json_decode($data);
            $city = $decoded->city;
            $forecast = $decoded->forecast;

            $info = "Aujourd'hui à {$city->name}, on prévoit {$forecast->rr10}mm (pas plus de {$forecast->rr1}mm en tous cas) de précipitations.";
        }

        return $this->render('versus/index.html.twig', [
            'info' => $info,
        ]);
    }
}
