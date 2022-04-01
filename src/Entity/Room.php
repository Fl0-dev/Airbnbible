<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $size;

    #[ORM\ManyToOne(targetEntity: Housing::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    private $housing;

    #[ORM\OneToMany(mappedBy: 'room', targetEntity: BedRoom::class, cascade: ["persist","remove"])]
    private $bedRooms;

    public function __construct()
    {
        $this->bedRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

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
            $bedRoom->setRoom($this);
        }

        return $this;
    }

    public function removeBedRoom(BedRoom $bedRoom): self
    {
        if ($this->bedRooms->removeElement($bedRoom)) {
            // set the owning side to null (unless already changed)
            if ($bedRoom->getRoom() === $this) {
                $bedRoom->setRoom(null);
            }
        }

        return $this;
    }
}
