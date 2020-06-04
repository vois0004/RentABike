<?php

namespace App\DataFixtures;

use App\Entity\Bike;
use App\Entity\Station;
use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /** @var Station $stationUn */
        $stationUn = new Station;
        $stationUn->setLatitude("49.25047927657");
        $stationUn->setLongitude("4.014799701573");
        $stationUn->setAdresse("Polyclinique des Bleuets");
        $stationUn->setState(Station::open);
        $stationUn->setCapacity(10);

        /** @var Station $stationDeux */
        $stationDeux = new Station;
        $stationDeux->setLatitude("49.261955875695");
        $stationDeux->setLongitude("4.0415313652133");
        $stationDeux->setAdresse("Rue Jacquart");
        $stationDeux->setState(Station::open);
        $stationDeux->setCapacity(20);

        /** @var Station $stationTrois */
        $stationTrois = new Station;
        $stationTrois->setLatitude("49.2532666348");
        $stationTrois->setLongitude("4.033800466555");
        $stationTrois->setAdresse("Cathédrale Notre-Dame");
        $stationTrois->setState(Station::close);
        $stationTrois->setCapacity(5);

        /** @var Station $stationQuatre */
        $stationQuatre = new Station;
        $stationQuatre->setLatitude("49.2597783814");
        $stationQuatre->setLongitude("4.022804826975");
        $stationQuatre->setAdresse("Gare de Reims");
        $stationQuatre->setState(Station::work_in_progress);
        $stationQuatre->setCapacity(30);



        for($i = 0 ; $i <8 ; $i++){
            /** @var Bike $bikeUn */
            $bikeUn = new Bike;
            $bikeUn->setStation($stationUn);
            $bikeUn->setCode($i);
            $manager->persist($bikeUn);
        }
        $stationUn->setDisponibility(8);

        for($i = 0 ; $i <3 ; $i++){
            /** @var Bike $bikeDeux */
            $bikeDeux = new Bike;
            $bikeDeux->setStation($stationDeux);
            $bikeDeux->setCode(20+$i);
            $manager->persist($bikeDeux);
        }
        $stationDeux->setDisponibility(3);

        for($i = 0 ; $i <4 ; $i++){
            /** @var Bike $bikeTrois */
            $bikeTrois = new Bike;
            $bikeTrois->setStation($stationTrois);
            $bikeTrois->setCode(30+$i);
            $manager->persist($bikeTrois);
        }
        $stationTrois->setDisponibility(4);

        for($i = 0 ; $i <8 ; $i++){
            /** @var Bike $bikeQuatre */
            $bikeQuatre = new Bike;
            $bikeQuatre->setStation($stationQuatre);
            $bikeQuatre->setCode(40+$i);
            $manager->persist($bikeQuatre);
        }
        $stationQuatre->setDisponibility(8);

        $manager->persist($stationUn);
        $manager->persist($stationDeux);
        $manager->persist($stationTrois);
        $manager->persist($stationQuatre);

        /** @var Subscription $subscriptionUn */
        $subscriptionUn = new Subscription();
        $subscriptionUn->setType(Subscription::OCCASIONNAL);
        $subscriptionUn->setPrice(10);
        $subscriptionUn->setFreeTime(30);

        /** @var Subscription $subscriptionDeux */
        $subscriptionDeux = new Subscription();
        $subscriptionDeux->setType(Subscription::MEDIUM);
        $subscriptionDeux->setPrice(15);
        $subscriptionDeux->setFreeTime(60);

        /** @var Subscription $subscriptionTrois */
        $subscriptionTrois = new Subscription();
        $subscriptionTrois->setType(Subscription::REGULAR);
        $subscriptionTrois->setPrice(20);
        $subscriptionTrois->setFreeTime(120);

        $manager->persist($subscriptionUn);
        $manager->persist($subscriptionDeux);
        $manager->persist($subscriptionTrois);

        $manager->flush();
    }
}
