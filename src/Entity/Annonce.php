<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnnonceRepository::class)
 * @ORM\Table(name="annonce", indexes={@ORM\Index(columns={"name", "description"}, flags={"fulltext"})})
 * @ORM\HasLifecycleCallbacks
 */
class Annonce
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private ?string $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 50,
     *      max = 2000,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isActive;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="annonces")
     */
    private ?Dictionnaire $diplome;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="annonces_experience")
     */
    private ?Dictionnaire $experience;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $reference;

    /**
     * @ORM\ManyToMany(targetEntity=Dictionnaire::class, inversedBy="annonces_type_contrat")
     * @ORM\JoinTable(name="annonces_contrat",
     *      joinColumns={@ORM\JoinColumn(name="annonce_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="dictionnaire_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $type_contrat;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="annonces_auteurs")
     * @ORM\JoinTable(name="annonces_auteurs",
     *      joinColumns={@ORM\JoinColumn(name="annonce_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $auteur;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="annonces_entreprise")
     */
    private ?Entreprise $entreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     *    message = "Le lien '{{ value }}' n'est pas au bon format",
     * )
     */
    private ?string $lien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas valide."
     * )
     */
    private ?string $adresse_email;

    /**
     * @ORM\ManyToOne(targetEntity=Offre::class, inversedBy="annonces")
     */
    private ?Offre $offre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dateLimiteCandidature;

    /**
     * @ORM\ManyToMany(targetEntity=Candidature::class, mappedBy="annonces")
     */
    private Collection $candidatures;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annonces")
     */
    private ?User $current_recruteur;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="favoris")
     */
    private Collection $favoris;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="annonce", cascade={"persist"})
     */
    private Collection $adresse;

    public function __construct()
    {
        $this->type_contrat = new ArrayCollection();
        $this->auteur = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->candidatures = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->adresse = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDiplome(): ?Dictionnaire
    {
        return $this->diplome;
    }

    public function setDiplome(?Dictionnaire $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getExperience(): ?Dictionnaire
    {
        return $this->experience;
    }

    public function setExperience(?Dictionnaire $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTypeContrat(): Collection
    {
        return $this->type_contrat;
    }

    public function addTypeContrat(Dictionnaire $typeContrat): self
    {
        if (!$this->type_contrat->contains($typeContrat)) {
            $this->type_contrat[] = $typeContrat;
        }

        return $this;
    }

    public function removeTypeContrat(Dictionnaire $typeContrat): self
    {
        $this->type_contrat->removeElement($typeContrat);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAuteur(): Collection
    {
        return $this->auteur;
    }

    public function addAuteur(User $auteur): self
    {
        if (!$this->auteur->contains($auteur)) {
            $this->auteur[] = $auteur;
        }

        return $this;
    }

    public function removeAuteur(User $auteur): self
    {
        $this->auteur->removeElement($auteur);

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

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getAdresseEmail(): ?string
    {
        return $this->adresse_email;
    }

    public function setAdresseEmail(?string $adresse_email): self
    {
        $this->adresse_email = $adresse_email;

        return $this;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getDateLimiteCandidature(): ?\DateTimeInterface
    {
        return $this->dateLimiteCandidature;
    }

    public function setDateLimiteCandidature(?\DateTimeInterface $dateLimiteCandidature): self
    {
        $this->dateLimiteCandidature = $dateLimiteCandidature;

        return $this;
    }

    public function isActive(): bool
    {
        if ($this->getIsActive() == 1 ){
            return true;
        }
        return false;
    }

    /**
     * @return Collection
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->addAnnonce($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->removeElement($candidature)) {
            $candidature->removeAnnonce($this);
        }

        return $this;
    }

    public function getNextRecruteur()
    {

        if ($this->getAuteur()->count() == 0) {
            return null;
        }

        if ($this->getCurrentRecruteur() == null) {
            return $this->getAuteur()->first();
        } else {
            foreach ($this->getAuteur() as $index => $_owner) {
                if ($this->getCurrentRecruteur() == $_owner) {
                    if ($this->getAuteur()->get($index + 1) == null) {
                        return $this->getAuteur()->first();
                    }

                    return $this->getAuteur()->get($index + 1);
                }
            }
        }

        // in other case remove first owners
        return $this->getAuteur()->first();
    }

    public function getCurrentRecruteur(): ?User
    {
        return $this->current_recruteur;
    }

    public function setCurrentRecruteur(?User $current_recruteur): self
    {
        $this->current_recruteur = $current_recruteur;

        return $this;
    }

    public function getCurrentJob(): bool
    {
        $now = new \DateTime('now');
        $result = false;

        if($now < $this->getDateLimiteCandidature()){
            $result = true;
        }
        return $result;
    }


    /**
     * @return Collection
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(User $favori): self
    {
        $this->favoris->removeElement($favori);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAdresse(): Collection
    {
        return $this->adresse;
    }

    public function addAdresse(Adresse $adresse): self
    {
        if (!$this->adresse->contains($adresse)) {
            $this->adresse[] = $adresse;
            $adresse->setAnnonce($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        if ($this->adresse->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getAnnonce() === $this) {
                $adresse->setAnnonce(null);
            }
        }

        return $this;
    }
}
