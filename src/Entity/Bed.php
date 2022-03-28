<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $nbPlace;

    #[ORM\OneToMany(mappedBy: 'bed', targetEntity: BedRoom::class)]
    private $bedRooms;

    public function __construct()
    {
        $this->bedRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    /**
     * @return Collection<int, BedRoom>
     */
    public function getBedRooms(): Collection
    {
        return $this->bedRooms;
    }

    public function addBedRoom(BedRoom $bedRoom): self
    {
        if (!$this->bedRooms->contains($bedRoom)) {
            $this->bedRooms[] = $bedRoom;
            $bedRoom->setBed($this);
        }

        return $this;
    }

    public function removeBedRoom(BedRoom $bedRoom): self
    {
        if ($this->bedRooms->removeElement($bedRoom)) {
            // set the owning side to null (unless already changed)
            if ($bedRoom->getBed() === $this) {
                $bedRoom->setBed(null);
            }
        }

        return $this;
    }
}
