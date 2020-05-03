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
     * @ORM\ManyToOne(targetEntity="App\Entity\Station", inversedBy="rides")
     * @ORM\JoinColumn(nullable=false)
     */
    private $StationBegin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Station", inversedBy="rides")
     */
    private $stationEnd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;



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
        return $this->StationBegin;
    }

    public function setStationBegin(?Station $StationBegin): self
    {
        $this->StationBegin = $StationBegin;

        return $this;
    }

    public function getStationEnd(): ?Station
    {
        return $this->stationEnd;
    }

    public function setStationEnd(?Station $stationEnd): self
    {
        $this->stationEnd = $stationEnd;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }


}
