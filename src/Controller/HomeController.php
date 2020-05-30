<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\Ride;
use App\Entity\Subscription;
use App\Entity\UserSubscription;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function home()
    {

        $rideRepository = $this->getDoctrine()->getRepository(Ride::class);
        $ride =  $rideRepository->findOneBy(['User'=>$this->getUser(), 'stationEnd'=>null]);


        if($userSubscription = $this->getUser()->getUserSubscription()!=null) {
            $userSubscription = $this->getUser()->getUserSubscription()->getSubscription();
        }

        $timeFree = 0;
        if($userSubscription!=null) {
            $timeFree = $userSubscription->getFreeTime();
            $rides = $rideRepository->findByUserAndToday($this->getUser());
            /** @var Ride $r */
            foreach ($rides as $r) {
                $timeFree -= $r->getDateEnd()->diff($r->getDate())->i;
            }
        }

        $timeSpend = 0;
        $rides = $rideRepository->findByUserAndThisWeek($this->getUser());
        foreach($rides as $r){
            $timeSpend += $r->getDateEnd()->diff($r->getDate())->i;
        }
        return $this->render('home.html.twig', [
            'user'=>$this->getUser(),
            'ride'=>$ride,
            'userSubscription'=>$userSubscription,
            'timeFree'=>$timeFree,
            'timeSpend'=>$timeSpend
        ]);
    }
}
