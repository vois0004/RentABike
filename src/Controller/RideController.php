<?php

namespace App\Controller;

use App\Entity\Bike;
use App\Entity\Payment;
use App\Entity\Station;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\ReturnBikeType;
use App\Form\RideType;
use App\Entity\Ride;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\Stripe;
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

        try{
            $this->denyAccessUnlessGranted('new', $ride);
        }
        catch (\Exception $e){
            return $this->redirectToRoute('app_return_bike');
        }

        if($station->getState()!='open'){
            throw new \Exception("Station non ouverte");
        }
        $ride->setStationBegin($station);

        $form = $this->createForm(RideType::class, $ride);

        $form->handleRequest($request);


        Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');

        $intent = PaymentIntent::create([
            'amount' => 5000,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'capture_method' => 'manual',
        ]);



        if ($form->isSubmitted() && $form->isValid()) {



            try {
                if ($form->get("paiementId")->getData() != null) {
                    $intent = \Stripe\PaymentIntent::retrieve(
                        $form->get("paiementId")->getData()
                    );
                    if($intent->status!=="requires_capture"){
                       throw new \Exception();
                    }

                    $payment = new Payment();
                    $payment->setIdPayment($form->get("paiementId")->getData());
                    $payment->setDateCre(new DateTime());
                    $payment->setStatus(Payment::IN_PROGRESS);

                    $ride->setPayment($payment);

                }
                else{
                    throw new \Exception("Fraude");
                }
            }
            catch (ApiErrorException $e) {
                throw new \Exception("Impossible de retrouver le paiement");
            }

            $ride = $form->getData();
            $ride->setStationBegin($ride->getBike()->getStation());
            $ride->setUser($this->getUser());
            $ride->setDate(new DateTime());

            $station = $ride->getBike()->getStation();
            $station->setDisponibility($station->getDisponibility()-1);

            $ride->getBike()->setStation(null);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ride);
            $entityManager->flush();

            return $this->redirectToRoute('app_return_bike');
        }

        return $this->render('ride/start.html.twig', [
            'form' => $form->createView(),
            'intent'=>$intent
        ]);
    }


    /**
     * @Route("/return_ride", name="app_return_bike")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ApiErrorException
     */
    public function return(Request $request)
    {
        $rideRepository = $this->getDoctrine()->getRepository(Ride::class);
        $ride =  $rideRepository->findOneBy(['User'=>$this->getUser(), 'stationEnd'=>null]);

        try{
            $this->denyAccessUnlessGranted('return', $ride);
        }
        catch (\Exception $e){
            return $this->redirectToRoute('app_map');
        }

        $form = $this->createForm(ReturnBikeType::class, $ride);
        $form->handleRequest($request);

        $dateBegin = $ride->getDate();

        if ($form->isSubmitted() && $form->isValid()) {

            $ride=$form->getData();

            if($ride->getStationEnd()->getState()!='open'){
                throw new \Exception("Station non ouverte");
            }

            //Get the client's subscription if he has one
            /** @var Subscription $subscriptionType */
            $subscriptionType;
            $timeFree =0;

            if($subscriptionType = $this->getUser()->getUserSubscription()!=null) {
                $subscriptionType = $this->getUser()->getUserSubscription()->getSubscription();
            }

            //Get the duration or the ride
            $date=new DateTime();
            $since_start=$date->diff($ride->getDate());
            $minutes = $since_start->days * 24 * 60 + $since_start->h * 60 + $since_start->i;
            //*100 pour test

            //Get the free time left for today
            if($subscriptionType!=null) {
                $timeFree = $subscriptionType->getFreeTime();
                $rides = $rideRepository->findByUserAndToday($this->getUser());
                /** @var Ride $r */
                foreach ($rides as $r) {
                    $timeFree -= $r->getDateEnd()->diff($r->getDate())->i;
                }
            }
            //Calculate price according to the duration of the ride substract the timefree
            //if timeFree < 0, no timefree left
            //if minutes - timesfree < 0, the ride is free
            $price = ($minutes - ($timeFree > 0 ? $timeFree : 0) > 0 ? $minutes : 0) * 1 *100;

            Stripe::setApiKey('sk_test_0V6MS5gcKG7l3dmk7htPzLLs00CBLB0DQH');


            $intent = PaymentIntent::retrieve($ride->getPayment()->getIdPayment());

            if($price>0){
                $intent->capture(['amount_to_capture' => $price]);
            }
            else {
                $intent->cancel();
            }

            $payment=$ride->getPayment();
            $payment->setStatus(Payment::DONE_FREE);
            $payment->setAmount($price);
            $payment->setDatePayment($date);
            $ride->setDateEnd(new DateTime());

            $bike = $ride->getBike();
            $bike->setStation($ride->getStationEnd());
            //check if the stationEnd is not full
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
            'dateBegin' => $dateBegin
        ]);
    }


    /**
     * @Route("/list_ride", name="app_list_ride")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ApiErrorException
     */
    public function show(Request $request)
    {
        $rideRepository = $this->getDoctrine()->getRepository(Ride::class);
        $rides =  $rideRepository->findAllByUser($this->getUser());

        return $this->render('ride/list.html.twig', [
            'rides'=>$rides
        ]);
    }


}
