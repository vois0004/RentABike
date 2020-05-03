<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Station;
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

        return $this->render('map/reims.html.twig', [
           'stations'=>$stations
        ]);
    }
}
