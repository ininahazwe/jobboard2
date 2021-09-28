<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    const TYPE_AVATAR = 1;
    const TYPE_CV = 2;
    const TYPE_MOTIVATION = 3;
    const TYPE_ILLUSTRATION = 4;
    const TYPE_LOGO = 5;

    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="logo")
     */
    private ?Entreprise $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Page $pages;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nameFile ="";

    /**
     * @ORM\ManyToOne(targetEntity=Annuaire::class, inversedBy="image")
     */
    private ?Annuaire $annuaire;

    /**
     * @ORM\ManyToOne(targetEntity=Candidature::class, inversedBy="cv")
     */
    private ?Candidature $candidature;

    /**
     * @ORM\ManyToOne(targetEntity=Candidature::class, inversedBy="lettre_motivation")
     */
    private ?Candidature $candidature_motivation;

    /**
     * @ORM\ManyToOne(targetEntity=Blog::class, inversedBy="images")
     */
    private ?Blog $blog;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $type;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="cv")
     */
    private ?Profile $profile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $namefree;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
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

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getPages(): ?Page
    {
        return $this->pages;
    }

    public function setPages(?Page $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNameFile(): ?string
    {
        return $this->nameFile;
    }

    public function setNameFile(string $nameFile): self
    {
        $this->nameFile = $nameFile;

        return $this;
    }

    public function getAnnuaire(): ?Annuaire
    {
        return $this->annuaire;
    }

    public function setAnnuaire(?Annuaire $annuaire): self
    {
        $this->annuaire = $annuaire;

        return $this;
    }

    public function getCandidature(): ?Candidature
    {
        return $this->candidature;
    }

    public function setCandidature(?Candidature $candidature): self
    {
        $this->candidature = $candidature;

        return $this;
    }

    public function getCandidatureMotivation(): ?Candidature
    {
        return $this->candidature_motivation;
    }

    public function setCandidatureMotivation(?Candidature $candidature_motivation): self
    {
        $this->candidature_motivation = $candidature_motivation;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): self
    {
        $this->blog = $blog;

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

  /**
   * @return string
   */
  public function getTypeName(): string
    {
      $type = $this->type;
      if ($type == 1){
          return 'Avatar';
      }else if($type == 2){
          return 'CV';
      }else if($type == 3){
          return 'Lettre de motivation';
      }else if($type == 4){
          return 'Image d\'illustration';
      }else if($type == 5){
        return 'Logo';
      }else{
          return 'Non renseigné';
      }
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getNamefree(): ?string
    {
        return $this->namefree;
    }

    public function setNamefree(?string $namefree): self
    {
        $this->namefree = $namefree;

        return $this;
    }
    public function __toString()
    {
        return $this->nameFile;
    }
    public function getFileSize(): ?string
    {
      $file = 'uploads/' . $this->name;

      $bytes = filesize($file);

      $label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
      for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
      return( round( $bytes, 0 ) . " " . $label[$i] );

    }


}
