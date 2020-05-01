<?php

namespace App\Controller;

use App\Entity\Bike;
use App\Form\RideType;
use App\Entity\Ride;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RideController extends AbstractController
{
    /**
     * @Route("/start_ride", name="app_start_ride")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function new(Request $request)
    {
        // creates a user object and initializes some data for this example
        $ride = new Ride();

        $form = $this->createForm(RideType::class, $ride);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $ride->setBike($this->getDoctrine()->getRepository(Bike::class)->findOneBy(['code'=>$form->get('code')->getData()]));
            $ride->setStationBegin($ride->getBike()->getStation());
            $ride->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ride);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('ride/start.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
