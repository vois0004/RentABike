<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BikeRepository")
 */
class Bike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Station", inversedBy="bikes")
     */
    private $Station;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStation(): ?Station
    {
        return $this->Station;
    }

    public function setStation(?Station $Station): self
    {
        $this->Station = $Station;

        return $this;
    }
}
