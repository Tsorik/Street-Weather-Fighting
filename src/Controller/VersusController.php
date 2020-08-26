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
        $city_name1 = $request->get('id1');
        $city_name2 = $request->get('id2');

        /* weather api request insee code */
        $insee1 = file_get_contents('https://api.meteo-concept.com/api/location/cities?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&search='. $city_name1 . '');
        $insee2 = file_get_contents('https://api.meteo-concept.com/api/location/cities?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&search='. $city_name2 . '');

        if ($insee1 !== false && $insee2 !== false) {
            $table_cities1 = json_decode($insee1);
            $cities1 = $table_cities1->{'cities'};
            $cities1 = $cities1[0];
            $insee_num1 = $cities1->{'insee'};

            $table_cities2 = json_decode($insee2);
            $cities2 = $table_cities2->{'cities'};
            $cities2 = $cities2[0];
            $insee_num2 = $cities2->{'insee'};
        }

        /* weather api request data */
        $data1 = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/0?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&insee=' . $insee_num1 . '');
        $data2 = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/0?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&insee=' . $insee_num2 . '');

        if ($data1 !== false && $data2 !== false) {
            $decoded1 = json_decode($data1);
            $forecast1 = $decoded1->forecast;

            $decoded2 = json_decode($data2);
            $forecast2 = $decoded2->forecast;
        }
        

        return $this->render('versus/index.html.twig', [
            'city_name1' => $city_name1,
            'city_name2' => $city_name2,
            'tmax1' => $forecast1->tmax,
            'tmax2' => $forecast2->tmax,
            'tmin1' => $forecast1->tmin,
            'tmin2' => $forecast2->tmin,
            'info' => $forecast1,
        ]);
    }
}
