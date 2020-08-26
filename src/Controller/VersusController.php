<?php

namespace App\Controller;

use App\Entity\Versus;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\VersusRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VersusController extends AbstractController
{
    /**
     * @Route("/versus", name="versus")
     */
    public function index(Request $request, EntityManagerInterface $manager, VersusRepository $versusRepository)
    {
        $city_name1 = $request->get('id1');
        $city_name2 = $request->get('id2');
        dump($city_name1);

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
        
        $versus_id = $versusRepository
        ->findOneBy([
            'city1' => $city_name1,
            'city2' => $city_name2,
        ]);
        
        $id_versus = $versus_id->id;
        
        if (!$versus_id) {
            $versus = new Versus();
            $versus->setCity1($city_name1);
            $versus->setCity2($city_name2);
            $manager->persist($versus);
            $manager->flush();
        }
        
        $comments = $versusRepository->find($id_versus);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('versus/index.html.twig', [
            'city_name1' => $city_name1,
            'city_name2' => $city_name2,
            'tmax1' => $forecast1->tmax,
            'tmax2' => $forecast2->tmax,
            'tmin1' => $forecast1->tmin,
            'tmin2' => $forecast2->tmin,
            'info' => $forecast1,
            'commentForm' => $form->createView(),
            'comments' => $comments

        ]);
    }
}
