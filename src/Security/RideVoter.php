<?php

namespace App\Security;

use App\Entity\Ride;
use App\Entity\User;
use App\Repository\RideRepository;
use App\Repository\StationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RideVoter extends Voter
{
    const NEW = 'new';
    const RETURN = 'return';
    private $rideRepository;

    public function __construct(RideRepository $rideRepository, StationRepository $stationRepository)
    {
        $this->rideRepository =$rideRepository;
        $this->stationRepository = $stationRepository;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::NEW, self::RETURN])) {
            return false;
        }
        if (!$subject instanceof Ride) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var Ride $ride */
        $ride = $subject;

        switch ($attribute) {
            case self::NEW:
                return $this->new($ride, $user);
            case self::RETURN:
                return $this->return($ride, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function new(Ride $ride, User $user)
    {
        $ride = $this->rideRepository->findOneBy(['User'=>$user,'stationEnd'=>null]);

        return $ride==null;
    }

    private function return(Ride $ride, User $user)
    {
        $ride = $this->rideRepository->findOneBy(['User'=>$user,'stationEnd'=>null]);

        return $ride!=null;
    }
}