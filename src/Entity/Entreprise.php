<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EntrepriseRepository::class)
 * @ORM\Table(name="entreprise", indexes={@ORM\Index(columns={"name", "description"}, flags={"fulltext"})})
 */
class Entreprise
{
    const EN_ATTENTE = 0;
    const ACCEPTEE = 1;
    const REFUSEE = 2;

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
    private string $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 50,
     *      max = 1000,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="entreprise")
     */
    private ?Dictionnaire $secteur;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Offre::class, mappedBy="entreprise")
     */
    private Collection $Offres;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="entreprises")
     * @ORM\JoinTable(name="entreprise_recruteur",
     *      joinColumns={@ORM\JoinColumn(name="entreprise_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $recruteurs;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="recruteurs_entreprise")
     * @ORM\JoinTable(name="entreprise_super_recruteur",
     *      joinColumns={@ORM\JoinColumn(name="entreprise_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $super_recruteurs;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="entreprise", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $logo;

    /**
     * @ORM\OneToMany(targetEntity=Facture::class, mappedBy="entreprise")
     */
    private Collection $factures;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $ref_client;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="entreprise")
     */
    private Collection $annonces_entreprise;

    /**
     * @ORM\OneToMany(targetEntity=Candidature::class, mappedBy="entreprise")
     */
    private Collection $candidatures;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?bool $regroupement_candidatures;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $numero_siret;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $numero_siren;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $taille;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $moderation;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="entreprise", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $adresse;

    public function __construct()
    {
        $this->Offres = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->recruteurs = new ArrayCollection();
        $this->super_recruteurs = new ArrayCollection();
        $this->logo = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->annonces_entreprise = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
        $this->adresse = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function getSecteur(): ?Dictionnaire
    {
        return $this->secteur;
    }

    public function setSecteur(?Dictionnaire $secteur): self
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return Collection
     */
    public function getOffres(): Collection
    {
        return $this->Offres;
    }

    public function addOffre(Offre $Offre): self
    {
        if (!$this->Offres->contains($Offre)) {
            $this->Offres[] = $Offre;
            $Offre->setEntreprise($this);
        }

        return $this;
    }

    public function removeOffre(Offre $Offre): self
    {
        if ($this->Offres->removeElement($Offre)) {
            // set the owning side to null (unless already changed)
            if ($Offre->getEntreprise() === $this) {
                $Offre->setEntreprise(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection
     */
    public function getRecruteurs(): Collection
    {
        return $this->recruteurs;
    }

    public function addRecruteur(User $recruteur): self
    {
        if (!$this->recruteurs->contains($recruteur)) {
            $this->recruteurs[] = $recruteur;
        }

        return $this;
    }

    public function removeRecruteur(User $recruteur): self
    {
        $this->recruteurs->removeElement($recruteur);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSuperRecruteurs(): Collection
    {
        return $this->super_recruteurs;
    }

    public function addSuperRecruteur(User $superRecruteur): self
    {
        if (!$this->super_recruteurs->contains($superRecruteur)) {
            $this->super_recruteurs[] = $superRecruteur;
        }

        return $this;
    }

    public function removeSuperRecruteur(User $superRecruteur): self
    {
        $this->super_recruteurs->removeElement($superRecruteur);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLogo(): Collection
    {
        return $this->logo;
    }

    public function addLogo(File $logo): self
    {
        if (!$this->logo->contains($logo)) {
            $this->logo[] = $logo;
            $logo->setEntreprise($this);
        }

        return $this;
    }

    public function removeLogo(File $logo): self
    {
        if ($this->logo->removeElement($logo)) {
            // set the owning side to null (unless already changed)
            if ($logo->getEntreprise() === $this) {
                $logo->setEntreprise(null);
            }
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getEntreprise() === $this) {
                $candidature->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getRegroupementCandidatures(): ?bool
    {
        return $this->regroupement_candidatures;
    }

    public function setRegroupementCandidatures(?bool $regroupement_candidatures): self
    {
        $this->regroupement_candidatures = $regroupement_candidatures;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlLastLogo(): string
    {
        $url = "";
        $logos = array();
        foreach ($this->getLogo() as $logo){
            $logos[] = $logo->getName();
        }

        $logo = end($logos);
        $url = "uploads/" . $logo;
        return $url ;
    }


    public function getNameLastLogo()
    {
        $logos = array();
        foreach ($this->getLogo() as $logo){
            $logos[] = $logo->getNameFile();
        }

        $logo = end($logos);
        $name = $logo;
        return $name ;
    }

    /**
     * @return Collection
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setEntreprise($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getEntreprise() === $this) {
                $facture->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getRefClient(): ?string
    {
        return $this->ref_client;
    }

    public function setRefClient(?string $ref_client): self
    {
        $this->ref_client = $ref_client;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnnoncesEntreprise(): Collection
    {
        return $this->annonces_entreprise;
    }

    public function addAnnoncesEntreprise(Annonce $annoncesEntreprise): self
    {
        if (!$this->annonces_entreprise->contains($annoncesEntreprise)) {
            $this->annonces_entreprise[] = $annoncesEntreprise;
            $annoncesEntreprise->setEntreprise($this);
        }

        return $this;
    }

    public function removeAnnoncesEntreprise(Annonce $annoncesEntreprise): self
    {
        if ($this->annonces_entreprise->removeElement($annoncesEntreprise)) {
            // set the owning side to null (unless already changed)
            if ($annoncesEntreprise->getEntreprise() === $this) {
                $annoncesEntreprise->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getMaxAnnonces()
    {
        $nombre = null;
        foreach($this->getOffres() as $offre){
            if ($offre->isActive()){
                if ($offre->getNombreOffres() == 0 ){
                    return 0;
                }
                $nombre = $nombre + $offre->getNombreOffres();
            }
        }
        return $nombre;
    }

    /**
     * @return mixed
     */
    public function getNumberAnnonces(): mixed
    {
        $nombre = 0;
        foreach($this->getAnnoncesEntreprise() as $annonce){
            if ($annonce->isActive()){
                $nombre = $nombre + 1;
            }
        }
        return $nombre;
    }

    /**
     * @return mixed
     */
    public function getNumberAnnoncesActive(): mixed
    {
        $nombre = 0;
        foreach($this->getAnnoncesEntreprise() as $annonce){
            if ($annonce->isActive()){
                if($annonce->getCurrentJob()){
                    $nombre = $nombre + 1;
                }
            }
        }
        return $nombre;
    }


    public function canCreateAnnonce(): bool
    {
        if (($this->getNumberAnnonces() < $this->getMaxAnnonces()) || ($this->getMaxAnnonces() == 0)){
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
            $candidature->setEntreprise($this);
        }

        return $this;
    }

    public function isRegroupementCandidature(): bool
    {
        if($this->regroupement_candidatures == 1 ){
            return true;
        }
        return false;
    }

    public function getNumeroSiret(): ?int
    {
        return $this->numero_siret;
    }

    public function setNumeroSiret(?int $numero_siret): self
    {
        $this->numero_siret = $numero_siret;

        return $this;
    }

    public function getNumeroSiren(): ?int
    {
        return $this->numero_siren;
    }

    public function setNumeroSiren(?int $numero_siren): self
    {
        $this->numero_siren = $numero_siren;

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(?int $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getModeration(): ?int
    {
        return $this->moderation;
    }

    public function setModeration(?int $moderation): self
    {
        $this->moderation = $moderation;

        return $this;
    }

    /**
     * @return string|void
     */
    public static function getModerationName()
    {
        if ($moderation = '0'){
            return 'En attente';
        }else if($moderation = '1'){
            return 'Acceptée';
        }else if($moderation = '2'){
            return 'Refusée';
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getAllRecruteurs(): ArrayCollection
    {
        $result = new ArrayCollection();

        foreach($this->getRecruteurs() as $recruteur){
            $result->add($recruteur);
        }
        foreach($this->getSuperRecruteurs() as $recruteur){
            if (!$result->contains($recruteur)) {
                $result->add($recruteur);
            }
        }
        return $result;
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
            $adresse->setEntreprise($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        if ($this->adresse->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getEntreprise() === $this) {
                $adresse->setEntreprise(null);
            }
        }

        return $this;
    }

}
