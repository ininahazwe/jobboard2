<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;


/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile {
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $birthdate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isRqth;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="diplome")
     */
    private ?Dictionnaire $diplome;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="experiences")
     */
    private ?Dictionnaire $experiences;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $zoneDeRecherche;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="metiers")
     */
    private ?Dictionnaire $metiers;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVisible;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isAmenagement;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="profile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="profile")
     */
    private ?Dictionnaire $civilite;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="profile", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url
     */
    private ?string $portfolio;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="profile", cascade={"persist"})
     */
    private Collection $cv;

    public function __construct() {
        $this->isVisible = false;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->adresse = new ArrayCollection();
        $this->cv = new ArrayCollection();
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getBirthdate(): \DateTime {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate): self {
        if (isset($birthdate)) {
            $this->birthdate = $birthdate;
        }

        return $this;
    }

    public function getIsRqth(): ?bool {
        return $this->isRqth;
    }

    public function setIsRqth(bool $isRqth): self {
        $this->isRqth = $isRqth;

        return $this;
    }

    public function getDiplome(): ?Dictionnaire {
        return $this->diplome;
    }

    public function setDiplome(?Dictionnaire $diplome): self {
        $this->diplome = $diplome;

        return $this;
    }

    public function getExperiences(): ?Dictionnaire {
        return $this->experiences;
    }

    public function setExperiences(?Dictionnaire $experiences): self {
        $this->experiences = $experiences;

        return $this;
    }

    public function getZoneDeRecherche(): ?string {
        return $this->zoneDeRecherche;
    }

    public function setZoneDeRecherche(?string $zoneDeRecherche): self {
        if (!empty($zoneDeRecherche)) {
            $this->zoneDeRecherche = $zoneDeRecherche;
        }

        return $this;
    }

    public function getMetiers(): ?Dictionnaire {
        return $this->metiers;
    }

    public function setMetiers(?Dictionnaire $metiers): self {
        $this->metiers = $metiers;

        return $this;
    }

    public function getIsVisible(): ?bool {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getIsAmenagement(): ?bool {
        return $this->isAmenagement;
    }

    public function setIsAmenagement(bool $isAmenagement): self {
        $this->isAmenagement = $isAmenagement;

        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        if (!empty($user)) {
            $this->user = $user;
        }

        return $this;
    }

    public function getCivilite(): ?Dictionnaire {
        return $this->civilite;
    }

    public function setCivilite(?Dictionnaire $civilite): self {
        $this->civilite = $civilite;

        return $this;
    }

    public function getPortfolio(): ?string {
        return $this->portfolio;
    }

    public function setPortfolio(?string $portfolio): self {
        $this->portfolio = $portfolio;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAdresse(): Collection {
        return $this->adresse;
    }

    public function addAdresse(Adresse $adresse): self {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
            $adresse->setProfile($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self {
        if ($this->adresse->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getProfile() === $this) {
                $adresse->setProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCv(): Collection {
        return $this->cv;
    }

    public function addCv(File $cv): self {
        if (!$this->cv->contains($cv)) {
            $this->cv[] = $cv;
            $cv->setProfile($this);
        }

        return $this;
    }

    public function removeCv(File $cv): self {
        if ($this->cv->removeElement($cv)) {
            // set the owning side to null (unless already changed)
            if ($cv->getProfile() === $this) {
                $cv->setProfile(null);
            }
        }

        return $this;
    }
}
