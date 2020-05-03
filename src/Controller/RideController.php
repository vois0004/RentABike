<?php

namespace App\Controller;

use App\Entity\Bike;
use App\Entity\Station;
use App\Form\ReturnBikeType;
use App\Form\RideType;
use App\Entity\Ride;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class RideController extends AbstractController
{
    /**
     * @Route("/start_ride/{id_station}", name="app_start_ride")
     * @ParamConverter("station", options={"id" = "id_station"})
     * @param Request $request
     * @param Station $station
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function rent(Request $request, Station $station)
    {
        // creates a user object and initializes some data for this example
        $ride = new Ride();
        $ride->setStationBegin($station);


        try{
            $this->denyAccessUnlessGranted('new', $ride);
        }
        catch (\Exception $e){
            return $this->redirectToRoute('app_return_bike');
        }

        $form = $this->createForm(RideType::class, $ride);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $bike = $this->getDoctrine()->getRepository(Bike::class)->findOneBy(['code'=>$form->get('code')->getData()]);

            $ride->setBike($bike);
            $ride->setStationBegin($ride->getBike()->getStation());
            $ride->setUser($this->getUser());
            $ride->setDate(new DateTime());

            $station = $bike->getStation();
            $station->setDisponibility($station->getDisponibility()-1);

            $bike->setStation(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ride);
            $entityManager->persist($station);
            $entityManager->persist($bike);
            $entityManager->flush();

            return $this->redirectToRoute('app_return_bike');
        }

        return $this->render('ride/start.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/return_ride", name="app_return_bike")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function return(Request $request)
    {
        // creates a user object and initializes some data for this example
        $ride =  $this->getDoctrine()->getRepository(Ride::class)->findOneBy(['User'=>$this->getUser(),'stationEnd'=>null]);

        try{
            $this->denyAccessUnlessGranted('return', $ride);
        }
        catch (\Exception $e){
            return $this->redirectToRoute('app_start_ride');
        }

        $form = $this->createForm(ReturnBikeType::class, $ride);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $ride=$form->getData();

            $bike = $ride->getBike();
            $bike->setStation($ride->getStationEnd());

            $station = $bike->getStation();
            $stationFull = ($station->getCapacity()==$station->getDisponibility())?true:false;
            if($stationFull){
                throw new \Exception("La capacité de la station est atteinte");
            }
            $station->setDisponibility($station->getDisponibility()+1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ride);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ride/return.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
