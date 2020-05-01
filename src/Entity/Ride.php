<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RideRepository")
 */
class Ride
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bike", inversedBy="rides")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bike;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="rides")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Station", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $stationBegin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Station", cascade={"persist", "remove"})
     */
    private $StationEnd;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $Duration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBike(): ?Bike
    {
        return $this->bike;
    }

    public function setBike(?Bike $bike): self
    {
        $this->bike = $bike;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getStationBegin(): ?Station
    {
        return $this->stationBegin;
    }

    public function setStationBegin(Station $stationBegin): self
    {
        $this->stationBegin = $stationBegin;

        return $this;
    }

    public function getStationEnd(): ?Station
    {
        return $this->StationEnd;
    }

    public function setStationEnd(?Station $StationEnd): self
    {
        $this->StationEnd = $StationEnd;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->Duration;
    }

    public function setDuration(?\DateTimeInterface $Duration): self
    {
        $this->Duration = $Duration;

        return $this;
    }
}
