<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
{
    const METHODE_CB = 1;
    const METHODE_VIREMENT = 2;
    const METHODE_CHEQUE = 3;
    const METHODE_AUTRE = 4;

    use ResourceId;
    use Timestapable;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="factures")
     */
    private ?Entreprise $entreprise;

    /**
     * @ORM\OneToMany(targetEntity=Offre::class, mappedBy="facture")
     */
    private Collection $offre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $TVA;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $prixTTC;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isPaid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $limiteDatePaid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $paymentDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $paymentMethods;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $linkFacture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $reference;

    public function __construct()
    {
        $this->offre = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
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
    public function getOffre(): Collection
    {
        return $this->offre;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offre->contains($offre)) {
            $this->offre[] = $offre;
            $offre->setFacture($this);
        }

        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        if ($this->offre->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getFacture() === $this) {
                $offre->setFacture(null);
            }
        }

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

    public function getTVA(): ?string
    {
        return $this->TVA;
    }

    public function setTVA(string $TVA): self
    {
        $this->TVA = $TVA;

        return $this;
    }

    public function getPrixTTC(): ?string
    {
        return $this->prixTTC;
    }

    public function setPrixTTC(string $prixTTC): self
    {
        $this->prixTTC = $prixTTC;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getLimiteDatePaid(): ?\DateTimeInterface
    {
        return $this->limiteDatePaid;
    }

    public function setLimiteDatePaid(?\DateTimeInterface $limiteDatePaid): self
    {
        $this->limiteDatePaid = $limiteDatePaid;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentMethods(): ?int
    {
        return $this->paymentMethods;
    }

    public function setPaymentMethods(?int $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;

        return $this;
    }

    public function getLinkFacture(): ?string
    {
        return $this->linkFacture;
    }

    public function setLinkFacture(?string $linkFacture): self
    {
        $this->linkFacture = $linkFacture;

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

    public function __toString(): string
    {
        return 'FHC-' . $this->reference;
    }

    public function getPaymentMethodsName(): string
    {
        if ($this->paymentMethods == self::METHODE_CB) {
            return" CB";
        } else if ($this->paymentMethods == self::METHODE_VIREMENT) {
            return" Virement";
        } else if ($this->paymentMethods == self::METHODE_CHEQUE) {
            return" Chèque";
        }else if ($this->paymentMethods == self::METHODE_AUTRE) {
            return " Autre";
        }else{
            return "";
        }
    }

    public function prixTVA(): string
    {
        $tva = $this->prix*$this->TVA/100;

        return $tva;
    }
    public function isPaidOk(): bool
    {
        if ($this->isPaid == 1){
            return true;
        }
        return  false;

    }
}
