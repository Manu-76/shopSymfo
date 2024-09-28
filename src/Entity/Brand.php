<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
// Le repository est une sorte de manuel qui contient des méthodes pour interroger la base de donnée (bdd)
use Doctrine\ORM\Mapping as ORM;
// Doctrine est le gestionnaire des manips que l'on souhaite faire sur la bdd (créer un objet, le modifier, lister les objets en bdd...)
use App\Repository\BrandRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

// ORM Object Relational Mapping
#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[Vich\Uploadable]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // Id créé & incrémenté automatiquement par doctrine
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)] // nullable = possible de ne pas injecter de valeur à la colonne
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Vich\UploadableField(mapping: 'brands', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    /**
     * @var Collection<int, Shoe>
     */
    #[ORM\OneToMany(targetEntity: Shoe::class, mappedBy: 'brand')]
    private Collection $shoe;

    public function __construct()
    {
        $this->shoe = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): static
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return Collection<int, Shoe>
     */
    public function getShoe(): Collection
    {
        return $this->shoe;
    }

    public function addShoe(Shoe $shoe): static
    {
        if (!$this->shoe->contains($shoe)) {
            $this->shoe->add($shoe);
            $shoe->setBrand($this);
        }

        return $this;
    }

    public function removeShoe(Shoe $shoe): static
    {
        if ($this->shoe->removeElement($shoe)) {
            // set the owning side to null (unless already changed)
            if ($shoe->getBrand() === $this) {
                $shoe->setBrand(null);
            }
        }

        return $this;
    }
}
