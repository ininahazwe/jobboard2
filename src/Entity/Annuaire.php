<?php

namespace App\Entity;

use App\Repository\AnnuaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnnuaireRepository::class)
 */
class Annuaire
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 10,
     *      max = 2000,
     *      minMessage = "un minimum de {{ limit }} caractères est requis",
     *      maxMessage = "{{ limit }} caractères sont la limite"
     * )
     */
    private ?string $content;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     *    message = "Le lien '{{ value }}' n'est pas au bon format",
     * )
     */
    private ?string $web_link;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="annuaire")
     */
    private Collection $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^[0-9]+$/i",
     *     message = "Seul les chiffres sont acceptés",
     * )
     */
    private ?string $telephone;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas valide."
     * )
     */
    private ?string $email;

    /**
     * @ORM\OneToMany(targetEntity=Adresse::class, mappedBy="annuaire", cascade={"persist"})
     */
    private Collection $adresse;

    /**
     * @ORM\ManyToMany(targetEntity=Dictionnaire::class, inversedBy="annuaires")
     * @ORM\JoinTable(name="annuaire_categorie",
     *      joinColumns={@ORM\JoinColumn(name="annuaire_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="dictionnaire_id", referencedColumnName="id")}
     *      )
     **/
    private Collection $categorie;

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->adresse = new ArrayCollection();
        $this->categorie = new ArrayCollection();

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

    /**
     * @return Collection
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
            $adresse->setAnnuaire($this);
        }

        return $this;
    }

    public function removeAdresse(Adresse $adresse): self
    {
        if ($this->adresse->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getAnnuaire() === $this) {
                $adresse->setAnnuaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Dictionnaire $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Dictionnaire $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlLastFile(): string
    {
        $url = "";
        $files = array();
        foreach ($this->getImage() as $file){
            $files[] = $file->getName();
        }

        $file = end($files);
        $url = "uploads/" . $file;
        return $url ;
    }

    /**
     * @return string
     */
    public function getNameLastFile(): string
    {
        $files = array();
        foreach ($this->getImage() as $file){
            $files[] = $file->getNameFile();
        }

        $file = end($files);
        $name = $file;
        return $name ;
    }
}
