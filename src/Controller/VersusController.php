<?php

namespace App\Controller;

use App\Entity\Versus;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\VersusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\VillesFranceFreeRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VersusController extends AbstractController
{
    /**
     * @Route("/versus", name="versus")
     */
    public function index(Request $request, EntityManagerInterface $manager, VersusRepository $versusRepository, VillesFranceFreeRepository $villesFranceFree)
    {
        global $data_details;

        $city_name1 = $request->get('id1');
        $city_name2 = $request->get('id2');

        /*      $city1 = $villesFranceFree->findBy(['villeSlug' => $city_name1]);
        $city2 = $villesFranceFree->findBy(['villeSlug' => $city_name2]);
        $insee_num1 = $city1[0];
        $insee_num1 = substr($insee_num1->getvilleCodePostal(), 0,5);
        dump($insee_num1);
        dump($city1); */

        /* weather api request insee code */
        $insee1 = file_get_contents('https://api.meteo-concept.com/api/location/cities?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&search=' . $city_name1 . '');
        $insee2 = file_get_contents('https://api.meteo-concept.com/api/location/cities?token=69465bc653ad80ed00bfa483dfc17a1b5f50bc9354c117c238f7d093454754fd&search=' . $city_name2 . '');

        if ($insee1 !== false && $insee2 !== false) {
            $table_cities1 = json_decode($insee1);
            $cities1 = $table_cities1->{'cities'};
            $cities1 = $cities1[0];
            $insee_num1 = $cities1->{'insee'};
            $city_name_str1 = $cities1->{'name'};
            $city_name_str1 = strstr($city_name_str1, ' ', true);
            if (!$city_name_str1) {
                $city_name_str1 = $cities1->{'name'};
            }

            $table_cities2 = json_decode($insee2);
            $cities2 = $table_cities2->{'cities'};
            $cities2 = $cities2[0];
            $insee_num2 = $cities2->{'insee'};
            $city_name_str2 = $cities2->{'name'};
            $city_name_str2 = strstr($city_name_str2, ' ', true);
            if (!$city_name_str2) {
                $city_name_str2 = $cities2->{'name'};
            }
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

        /* find versus page id existing or create */
        $result = $versusRepository->verification_versus($city_name1, $city_name2);

        if (!$result) {
            $versus = new Versus();
            $versus->setCity1($city_name1);
            $versus->setCity2($city_name2);
            $manager->persist($versus);
            $manager->flush();
        } else {
            $result = $result[0];
            $versus = $result;
        }
        $comments = $versusRepository->find($versus->id);
        if (!$comments) {
            $comments = "Pas de commentaires !";
        }

        /* loading comments */
        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime());
        $comment->setVersus($versus);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $empty_form = $this->createForm(CommentType::class);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();
        }
        
        return $this->render('versus/index.html.twig', [
            'city_name1' => $city_name_str1,
            'city_name2' => $city_name_str2,
            'insee1' => $insee_num1,
            'insee2' => $insee_num2,
            'tmax1' => $forecast1->tmax,
            'tmax2' => $forecast2->tmax,
            'tmin1' => $forecast1->tmin,
            'tmin2' => $forecast2->tmin,
            'info' => $forecast1,
            'commentForm' => $empty_form->createView(),
            'comments' => $comments,
            'weather1' => $forecast1->weather,
            'weather2' => $forecast2->weather,
            'sunhours1' => $forecast1->sun_hours,
            'sunhours2' => $forecast2->sun_hours,
            'probawind1' => $forecast1->probawind70,
            'probawind2' => $forecast2->probawind70,
            'probarain1' => $forecast1->probarain,
            'probarain2' => $forecast2->probarain
        ]);
    }
}
