<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DetailsController extends AbstractController
{
    /**
     * @Route("/details/{slug}/{insee}", name="details")
     */
    public function index($slug, $insee, Request $request)
    {
        $weather_date = $request->get('weather_date');
        if (!$weather_date) {
            $weather_date = 0;
        }
        
         /* weather api request data */
         $data = file_get_contents('https://api.meteo-concept.com/api/forecast/daily/' . $weather_date . '?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&insee=' . $insee . '');
 
         if ($data !== false) {
             $decoded = json_decode($data);
             $forecast = $decoded->forecast;
             $city = $decoded->city;
         }
         $city_name = strstr($city->name, ' ', true);
         if(!$city_name){
             $city_name = $city->name;
         }
         dump($city_name);
        
        return $this->render('details/index.html.twig', [
            'city_name' => $city_name,
            'lng' => $city->longitude,
            'lat' => $city->latitude,
            'tmax' => $forecast->tmax,
            'tmin' => $forecast->tmin,
            'weather1' => $forecast->weather,
            'sunhours' => $forecast->sun_hours,
            'probawind' => $forecast->probawind70,
            'probarain' => $forecast->probarain,
            'slug' => $slug,
            'insee' => $insee
        ]);

    }
}
