<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSubscriptionRepository")
 */
class UserSubscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateBeginning;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="userSubscription", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscription")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Subscription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeSubscription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBeginning(): ?\DateTimeInterface
    {
        return $this->dateBeginning;
    }

    public function setDateBeginning(\DateTimeInterface $dateBeginning): self
    {
        $this->dateBeginning = $dateBeginning;

        return $this;
    }



    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->Subscription;
    }

    public function setSubscription(?Subscription $Subscription): self
    {
        $this->Subscription = $Subscription;

        return $this;
    }

    public function getStripeSubscription(): ?string
    {
        return $this->stripeSubscription;
    }

    public function setStripeSubscription(string $stripeSubscription): self
    {
        $this->stripeSubscription = $stripeSubscription;

        return $this;
    }
}
