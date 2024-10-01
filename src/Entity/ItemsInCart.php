<?php

namespace App\Entity;

use App\Repository\ItemsInCartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemsInCartRepository::class)]
class ItemsInCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $sizeSelected = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $totalPriceItem = null;

    #[ORM\ManyToOne(inversedBy: 'itemsInCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Price $price = null;

    #[ORM\ManyToOne(inversedBy: 'itemsInCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSizeSelected(): ?int
    {
        return $this->sizeSelected;
    }

    public function setSizeSelected(int $sizeSelected): static
    {
        $this->sizeSelected = $sizeSelected;

        return $this;
    }

    public function getTotalPriceItem(): ?string
    {
        return $this->totalPriceItem;
    }

    public function setTotalPriceItem(string $totalPriceItem): static
    {
        $this->totalPriceItem = $totalPriceItem;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
