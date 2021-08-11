<?php

namespace App\Entity;

use App\Repository\AnnuaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=AnnuaireRepository::class)
 */
class Annuaire
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $content;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $web_link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $code_postal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="annuaire")
     */
    private Collection $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $telephone;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="annuaires")
     */
    private ?Dictionnaire $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getWebLink(): ?string
    {
        return $this->web_link;
    }

    public function setWebLink(?string $web_link): self
    {
        $this->web_link = $web_link;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(?string $addresse): self
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(?string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(File $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setAnnuaire($this);
        }

        return $this;
    }

    public function removeImage(File $image): self
    {
        if ($this->image->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAnnuaire() === $this) {
                $image->setAnnuaire(null);
            }
        }

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCategorie(): ?Dictionnaire
    {
        return $this->categorie;
    }

    public function setCategorie(?Dictionnaire $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
