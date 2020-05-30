<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{

    const IN_PROGRESS ='in progress',
        DONE = 'done',
        DONE_FREE = 'done_free';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idPayment;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datePayment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDateCre(): ?\DateTimeInterface
    {
        return $this->dateCre;
    }

    public function setDateCre(\DateTimeInterface $dateCre): self
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    public function getIdPayment(): ?string
    {
        return $this->idPayment;
    }

    public function setIdPayment(string $idPayment): self
    {
        $this->idPayment = $idPayment;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDatePayment(): ?\DateTimeInterface
    {
        return $this->datePayment;
    }

    public function setDatePayment(?\DateTimeInterface $datePayment): self
    {
        $this->datePayment = $datePayment;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
