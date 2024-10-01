<?php

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $size = null;

    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shoe $shoe = null;

    /**
     * @var Collection<int, ItemsInCart>
     */
    #[ORM\OneToMany(targetEntity: ItemsInCart::class, mappedBy: 'price', orphanRemoval: true)]
    private Collection $itemsInCarts;

    public function __construct()
    {
        $this->itemsInCarts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSize(): ?array
    {
        return $this->size;
    }

    public function setSize(?array $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getShoe(): ?Shoe
    {
        return $this->shoe;
    }

    public function setShoe(?Shoe $shoe): static
    {
        $this->shoe = $shoe;

        return $this;
    }

    /**
     * @return Collection<int, ItemsInCart>
     */
    public function getItemsInCarts(): Collection
    {
        return $this->itemsInCarts;
    }

    public function addItemsInCart(ItemsInCart $itemsInCart): static
    {
        if (!$this->itemsInCarts->contains($itemsInCart)) {
            $this->itemsInCarts->add($itemsInCart);
            $itemsInCart->setPrice($this);
        }

        return $this;
    }

    public function removeItemsInCart(ItemsInCart $itemsInCart): static
    {
        if ($this->itemsInCarts->removeElement($itemsInCart)) {
            // set the owning side to null (unless already changed)
            if ($itemsInCart->getPrice() === $this) {
                $itemsInCart->setPrice(null);
            }
        }

        return $this;
    }
}
