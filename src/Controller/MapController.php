<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Ride;
use App\Entity\Station;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    /**
     * @Route("/map", name="app_map")
     */
    public function map()
    {

        $stations = $this->getDoctrine()->getRepository(Station::class)->findAll();
        $rideRepository = $this->getDoctrine()->getRepository(Ride::class);
        $ride =  $rideRepository->findOneBy(['User'=>$this->getUser(), 'stationEnd'=>null]);

        return $this->render('map/reims.html.twig', [
           'stations'=>$stations,
            'user'=>$this->getUser(),
            'ride' => $ride
        ]);
    }

    /**
     * @Route("/add_fav/{id_station}", name="app_add_fav")
     * @param Station $station
     * @ParamConverter("station", options={"id" = "id_station"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addFav(Station $station){
        $user = $this->getUser();
        $oldStation = '';
        try{
            $oldStation = $this->getUser()->getFavStation();
        }
        catch(Exception $e){}

        $user->setFavStation($station);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


            return $this->redirectToRoute('app_map');

    }
}
