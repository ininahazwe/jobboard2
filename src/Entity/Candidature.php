<?php

namespace App\Entity;

use App\Repository\CandaditureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandaditureRepository::class)
 */
class Candidature
{
    use ResourceId;
    use Timestapable;

    const TYPE_EXTERNE = 1;
    const TYPE_MAIL = 2;
    const TYPE_INTERNE = 3;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $motivation;

     /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="candidatures")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $candidat;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="candidatures")
     */
    private ?Entreprise $entreprise;

    /**
     * @ORM\ManyToMany(targetEntity=Annonce::class, inversedBy="candidatures")
     * @ORM\JoinTable(name="candidature_annonce",
     *      joinColumns={@ORM\JoinColumn(name="candidature_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="annonce_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $annonces;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="candidatures_recruteur")
     */
    private $recruteur;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="candidature")
     */
    private $cv;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="candidature_motivation")
     */
    private $lettre_motivation;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->setCreatedAt(new \DateTimeImmutable('now'));
        $this->cv = new ArrayCollection();
        $this->lettre_motivation = new ArrayCollection();
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

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): self
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getCandidat(): ?User
    {
        return $this->candidat;
    }

    public function setCandidat(?User $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        $this->annonces->removeElement($annonce);

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRecruteur(): ?User
    {
        return $this->recruteur;
    }

    public function setRecruteur(?User $recruteur): self
    {
        $this->recruteur = $recruteur;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getCv(): Collection
    {
        return $this->cv;
    }

    public function addCv(File $cv): self
    {
        if (!$this->cv->contains($cv)) {
            $this->cv[] = $cv;
            $cv->setCandidature($this);
        }

        return $this;
    }

    public function removeCv(File $cv): self
    {
        if ($this->cv->removeElement($cv)) {
            // set the owning side to null (unless already changed)
            if ($cv->getCandidature() === $this) {
                $cv->setCandidature(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getLettreMotivation(): Collection
    {
        return $this->lettre_motivation;
    }

    public function addLettreMotivation(File $lettreMotivation): self
    {
        if (!$this->lettre_motivation->contains($lettreMotivation)) {
            $this->lettre_motivation[] = $lettreMotivation;
            $lettreMotivation->setCandidatureMotivation($this);
        }

        return $this;
    }

    public function removeLettreMotivation(File $lettreMotivation): self
    {
        if ($this->lettre_motivation->removeElement($lettreMotivation)) {
            // set the owning side to null (unless already changed)
            if ($lettreMotivation->getCandidatureMotivation() === $this) {
                $lettreMotivation->setCandidatureMotivation(null);
            }
        }

        return $this;
    }
}
