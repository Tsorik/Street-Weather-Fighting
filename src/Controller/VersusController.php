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
        $city_name1 = $request->get('id1');
        $city_name2 = $request->get('id2');

        /* $city1 = $villesFranceFree->findBy(['villeSlug' => $city_name1]);
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
            $city1 = $decoded1->city;

            $decoded2 = json_decode($data2);
            $forecast2 = $decoded2->forecast;
            $city2 = $decoded2->city;
        }
        /* find versus page id existing or create */
      /*   $versus_id = $versusRepository
            ->findOneBy([
                'city1' => $city_name1,
                'city2' => $city_name2,
            ]);
 */
        $queryBuilder = $this->$manager->getRepository(VersusRepository::class)
            ->createQueryBuilder('u');

        $result = $queryBuilder->select('u')
            ->from('versus', 'u')
            ->where('u.city1 = city_name1 and u.city2 = city_name2 or u.city1 = city_name2 and u.city2 = city_name1')
            ->setParameters(array("city_name1" => $city_name1, "city_name2" => $city_name2))
            ->getQuery()
            ->getResult();
dump($result);
        if (!$result) {
            $versus = new Versus();
            $versus->setCity1($city_name1);
            $versus->setCity2($city_name2);
            $manager->persist($versus);
            $manager->flush();
        } else {
            $versus = $result;
        }
        $comments = $versusRepository->find($versus->id);
        if (!$comments) {
            $comments = "Pas de commentaires !";
        }

        $comment = new Comment();
        $comment->setCreatedAt(new \DateTime());
        $comment->setVersus($versus_id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('versus/index.html.twig', [
            'city_name1' => $city1->name,
            'city_name2' => $city2->name,
            'tmax1' => $forecast1->tmax,
            'tmax2' => $forecast2->tmax,
            'tmin1' => $forecast1->tmin,
            'tmin2' => $forecast2->tmin,
            'info' => $forecast1,
            'commentForm' => $form->createView(),
            'comments' => $comments,
            'weather1' => $forecast1->weather,
            'weather2' => $forecast2->weather
        ]);
    }
}
