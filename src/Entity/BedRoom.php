<?php

namespace App\Entity;

use App\Repository\BedRoomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BedRoomRepository::class)]
class BedRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\ManyToOne(targetEntity: Bed::class, inversedBy: 'bedRooms')]
    #[ORM\JoinColumn(nullable: false)]
    private $bed;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'bedRooms')]
    private $room;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getBed(): ?Bed
    {
        return $this->bed;
    }

    public function setBed(?Bed $bed): self
    {
        $this->bed = $bed;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }
}
