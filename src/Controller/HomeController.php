<?php

namespace App\Controller;

use App\Entity\Versus;
use App\Form\VillesFranceFreeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {

        $city_name1 = $request->get('id1');
        $city_name2 = $request->get('id2');

        // $response = $httpClient->request('GET', 'https://api.teleport.org/api/cities/?search=quimperl%C3%A9');
        // dd($response->getContent());

        $citySearch = new Versus();

        $formSearchCity = $this->createForm(VillesFranceFreeType::class, $citySearch);

        $formSearchCity->handleRequest($request);

        if ($formSearchCity->isSubmitted() && $formSearchCity->isValid()) {

            return $this->redirect('versus?id1='.$city_name1.'&id2='.$city_name2.'');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'formSearchCity' => $formSearchCity->createView(),

        ]);
    }
}
