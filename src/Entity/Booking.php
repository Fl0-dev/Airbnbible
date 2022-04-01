<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $entryDate;

    #[ORM\Column(type: 'datetime')]
    private $exitDate;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $modifiedAt;

    #[ORM\Column(type: 'integer')]
    private $totalPrice;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\ManyToOne(targetEntity: Housing::class, cascade: ['persist','remove'], inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private $housing;

    #[ORM\Column(type: 'integer')]
    private $NbGuest;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $journeyTime;

    #[Assert\Callback]
    public function validateDate(ExecutionContextInterface $context){
        if($this->getEntryDate() > $this->getExitDate()) {
            $context->buildViolation("La date d'entrée doit être antérieure à la date de sortie")
                ->atPath('entryDate')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function setEntryDate(\DateTimeInterface $entryDate): self
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    public function getExitDate(): ?\DateTimeInterface
    {
        return $this->exitDate;
    }

    public function setExitDate(\DateTimeInterface $exitDate): self
    {
        $this->exitDate = $exitDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getHousing(): ?Housing
    {
        return $this->housing;
    }

    public function setHousing(?Housing $housing): self
    {
        $this->housing = $housing;

        return $this;
    }

    public function getNbGuest(): ?int
    {
        return $this->NbGuest;
    }

    public function setNbGuest(int $NbGuest): self
    {
        $this->NbGuest = $NbGuest;

        return $this;
    }

    public function getJourneyTime(): ?int
    {
        return $this->journeyTime;
    }

    public function setJourneyTime(?int $journeyTime): self
    {
        $this->journeyTime = $journeyTime;

        return $this;
    }

}
