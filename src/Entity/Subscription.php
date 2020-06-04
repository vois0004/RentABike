<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 */
class Subscription
{

    const OCCASIONNAL = 'occasionnal',
        MEDIUM = 'medium',
        REGULAR = 'regular';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;


    /**
     * @ORM\Column(type="integer")
     */
    private $freeTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripePlan;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }



    public function __construct()
    {

    }

    public function getFreeTime(): ?int
    {
        return $this->freeTime;
    }

    public function setFreeTime(int $freeTime): self
    {
        $this->freeTime = $freeTime;

        return $this;
    }

    public function getStripePlan(): ?string
    {
        return $this->stripePlan;
    }

    public function setStripePlan(?string $stripePlan): self
    {
        $this->stripePlan = $stripePlan;

        return $this;
    }


}
