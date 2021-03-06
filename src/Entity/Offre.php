<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OffreRepository::class)
 */
class Offre
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private string $formule;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $nombre_offres;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $debutContratAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $finContratAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isCvTheque;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $prix;

    /**
     * @ORM\Column(type="boolean")
     */
    private string $isFacture;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="Offres")
     */
    private Entreprise $entreprise;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $nombre_recruteurs;

    /**
     * @ORM\ManyToOne(targetEntity=ModeleOffreCommerciale::class, inversedBy="offre", cascade={"persist", "remove"})
     */
    private ?ModeleOffreCommerciale $modele_offre_commerciale;

    /**
     * @ORM\ManyToOne(targetEntity=Facture::class, inversedBy="offre")
     */
    private ?Facture $facture;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="offre")
     */
    private Collection $annonces;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="offre")
     */
    private Collection $orders;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->orders = new ArrayCollection();

    }

    public function getFormule(): ?string
    {
        return $this->formule;
    }

    public function setFormule(string $formule): self
    {
        $this->formule = $formule;

        return $this;
    }

    public function getNombreOffres(): ?int
    {
        return $this->nombre_offres;
    }

    public function setNombreOffres(?int $nombre_offres): self
    {
        $this->nombre_offres = $nombre_offres;

        return $this;
    }

    public function getDebutContratAt(): ?\DateTimeInterface
    {
        return $this->debutContratAt;
    }

    public function setDebutContratAt($debutContratAt): self
    {
        $this->debutContratAt = $debutContratAt;

        return $this;
    }

    public function getFinContratAt(): ?\DateTimeInterface
    {
        return $this->finContratAt;
    }

    public function setFinContratAt($finContratAt): self
    {
        $this->finContratAt = $finContratAt;

        return $this;
    }

    public function getIsCvTheque(): ?bool
    {
        return $this->isCvTheque;
    }

    public function setIsCvTheque(bool $isCvTheque): self
    {
        $this->isCvTheque = $isCvTheque;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIsFacture(): ?bool
    {
        return $this->isFacture;
    }

    public function setIsFacture(bool $isFacture): self
    {
        $this->isFacture = $isFacture;

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

    public function getNombreRecruteurs(): ?int
    {
        return $this->nombre_recruteurs;
    }

    public function setNombreRecruteurs(?int $nombre_recruteurs): self
    {
        $this->nombre_recruteurs = $nombre_recruteurs;

        return $this;
    }

    public function getModeleOffreCommerciale(): ?ModeleOffreCommerciale
    {
        return $this->modele_offre_commerciale;
    }

    public function setModeleOffreCommerciale(?ModeleOffreCommerciale $modele_offre_commerciale): self
    {
        $this->modele_offre_commerciale = $modele_offre_commerciale;

        return $this;
    }
    public function isModele(): bool {
        if ($this->getModeleOffreCommerciale()){
            return true;

        }
        return false;
    }

    public function isActive(): bool {
        $now = new \DateTime('now');
        if ($now < $this->getFinContratAt()){
            return true;
        }
        return false;
    }

    public function isActiveNotFactured(): bool {

        $now = new \DateTime('now');
        if ($now < $this->getFinContratAt() && $this->getIsFacture() == true ){
            return true;
        }
        return false;
    }


    public function isPassed(): bool {
        $now = new \DateTime('now');
        if ($now > $this->getFinContratAt()){
            return true;
        }
        return false;
    }

    public function getStatusName(): string {
        if ($this->isActive()){
            return "Active";
        }
        if ($this->isPassed()){
        return "Passée";
    }
        return "non renseignée";
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

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
            $annonce->setOffre($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getOffre() === $this) {
                $annonce->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }
    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setOffre($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getOffre() === $this) {
                $order->setOffre(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
