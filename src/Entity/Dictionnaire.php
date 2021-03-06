<?php

namespace App\Entity;

use App\Repository\DictionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DictionnaireRepository::class)
 * @ORM\Table(name="dictionnaire")
 */
class Dictionnaire
{
    const TYPE_CONTRACT = 'contract';
    const TYPE_DIPLOMA = 'diploma';
    const TYPE_EXPERIENCE = 'experience';
    const TYPE_LANGUAGE = 'language';
    const TYPE_LEVEL = 'level';
    const TYPE_REGION = 'region';
    const TYPE_CONTRACT_FORMATION = 'contract formation';
    const TYPE_SOURCE = 'source';
    const TYPE_START = 'debut';
    const TYPE_BUDGET = 'budget';
    const TYPE_SECTEUR = 'secteur';
    const TYPE_DURATION = 'duration';
    const TYPE_REFUS = 'refus';
    const TYPE_METIER = 'metier';
    const TYPE_ENTITE = 'entite';
    const TYPE_SOL = 'couleur_sol';
    const TYPE_PERSONNAGE = 'personnage';
    const TYPE_COMPTOIRE = 'comptoir';
    const TYPE_MOBILIER = 'mobilier';
    const TYPE_PLANTE = 'plante';
    const TYPE_TRANSPORT = 'transport';
    const TYPE_ENTRETIEN = 'entretien';
    const TYPE_FORMATION = 'formation';
    const TYPE_FORMATION1 = 'formation1';
    const TYPE_FORMATION2 = 'formation2';
    const TYPE_FORMATION3 = 'formation3';
    const TYPE_FORMATION4 = 'formation4';
    const TYPE_FORMATION5 = 'formation5';
    const TYPE_CATEGORIE_ANNUAIRE = 'categorie_annuaire';
    const TYPE_CATEGORIE_AGENDA = 'categorie_agenda';
    const TYPE_CATEGORIE_BLOG = 'categorie_blog';
    const TYPE_CATEGORIE_CIVILITE = 'civilite';

    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    private string $value = '';

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="diplome")
     */
    private Collection $annonces;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="experience")
     */
    private Collection $annonces_experience;

    /**
     * @ORM\ManyToMany(targetEntity=Annonce::class, mappedBy="type_contrat")
     */
    private Collection $annonces_type_contrat;

    /**
     * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="civilite")
     */
    private Collection $profile;

    /**
     * @ORM\ManyToMany(targetEntity=Blog::class, mappedBy="categorie")
     */
    private Collection $blog;

    /**
     * @ORM\OneToMany(targetEntity=Entreprise::class, mappedBy="secteur")
     */
    private Collection $entreprise;

    /**
     * @ORM\OneToMany(targetEntity=Agenda::class, mappedBy="categorie")
     */
    private Collection $agenda;

    /**
     * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="metiers")
     */
    private Collection $metiers;

    /**
     * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="diplome")
     */
    private Collection $diplome;

  /**
   * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="experiences")
   */
  private Collection $experiences;

  /**
   * @ORM\ManyToMany(targetEntity=Annuaire::class, mappedBy="categorie")
   */
  private Collection $annuaires;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable('now'));
        $this->annonces = new ArrayCollection();
        $this->annonces_experience = new ArrayCollection();
        $this->annonces_type_contrat = new ArrayCollection();
        $this->profile = new ArrayCollection();
        $this->metiers = new ArrayCollection();
        $this->diplome = new ArrayCollection();
        $this->blog = new ArrayCollection();
        $this->entreprise = new ArrayCollection();
        $this->agenda = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->annuaires = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
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
            $annonce->setDiplome($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getDiplome() === $this) {
                $annonce->setDiplome(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnnoncesExperience(): Collection
    {
        return $this->annonces_experience;
    }

    public function addAnnoncesExperience(Annonce $annoncesExperience): self
    {
        if (!$this->annonces_experience->contains($annoncesExperience)) {
            $this->annonces_experience[] = $annoncesExperience;
            $annoncesExperience->setExperience($this);
        }

        return $this;
    }

    public function removeAnnoncesExperience(Annonce $annoncesExperience): self
    {
        if ($this->annonces_experience->removeElement($annoncesExperience)) {
            // set the owning side to null (unless already changed)
            if ($annoncesExperience->getExperience() === $this) {
                $annoncesExperience->setExperience(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnnoncesTypeContrat(): Collection
    {
        return $this->annonces_type_contrat;
    }

    public function addAnnoncesTypeContrat(Annonce $annoncesTypeContrat): self
    {
        if (!$this->annonces_type_contrat->contains($annoncesTypeContrat)) {
            $this->annonces_type_contrat[] = $annoncesTypeContrat;
            $annoncesTypeContrat->addTypeContrat($this);
        }

        return $this;
    }

    public function removeAnnoncesTypeContrat(Annonce $annoncesTypeContrat): self
    {
        if ($this->annonces_type_contrat->removeElement($annoncesTypeContrat)) {
            $annoncesTypeContrat->removeTypeContrat($this);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getAvailabilityStatusList(): array
    {
        return array(
            '0' => 'Accessible',
            '1' => 'No accessible',
            '2' => 'Accessible under conditions'
        );
    }

    /**
     * Get experience field value
     * @param $value
     * @return string
     */
    public static function getExperienceName($value): string
    {
        switch ($value) {
            case 1:
                return 'Exp??rience non pr??cis??e';
            case 2:
                return "Moins d'1 an";
            case 3:
                return '1 ?? 2 ans';
            case 4:
                return '2 ?? 5 ans';
            case 5:
                return '5 ?? 10 ans';
            case 6:
                return '10 ?? 15 ans';
            case 7:
                return 'Plus de 15 ans';
            case 8:
                return 'Tout niveau d\'exp??rience';
            case 9:
                return 'Etudiant / En formation';
            default:
                return 'Non renseign??';
        }
    }

    /**
     * Get diploma field text
     * @param $value
     * @return string
     */
    public static function getDiplomaName($value): string
    {
        switch ($value) {
            case 1:
                return 'Pas de dipl??me requis';
            case 2:
                return 'CAP / BEP';
            case 3:
                return 'BAC / BAC Pro';
            case 4:
                return 'Bac + 1';
            case 5:
                return 'Bac + 2';
            case 6:
                return 'Bac + 3';
            case 7:
                return 'Bac + 4';
            case 8:
                return 'Bac + 5 et plus';
            default:
                return 'Non renseign??';
        }
    }

//    /**
//     * Get contract name by value
//     * @param $value
//     * @return string
//     */
//    public static function getContractNameByValue($value): string
//    {
//        if (is_array($value)) {
//            return '';
//        }
//
//        switch ($value) {
//            case 1:
//                return 'CDI';
//            case 2:
//                return 'CDD';
//            case 3:
//                return 'Interim';
//            case 4:
//                return 'Stage';
//            case 5:
//                return "Contrat d'apprentissage";
//            case 6:
//                return 'Contrat de professionalisation';
//            case 7:
//                return 'Autre';
//            case 8:
//                return 'Emploi saisonnier';
//            default:
//                return '';
//        }
//    }

//    /**
//     * Get contract field value
//     * @param $value
//     * @return string
//     */
//    public static function getContractName($value): string
//    {
//        if (!is_array($value)) {
//            $value = explode('-', $value);
//        }
//
//        $result = array();
//        foreach ($value as $_value) {
//            switch ($_value) {
//                case 1:
//                    $result[] = 'CDI';
//                case 2:
//                    $result[] = 'CDD';
//                case 3:
//                    $result[] = 'Interim';
//                case 4:
//                    $result[] = 'Stage';
//                case 5:
//                    $result[] = "Contrat d'apprentissage";
//                case 6:
//                    $result[] = 'Contrat de professionalisation';
//                case 7:
//                    $result[] = 'Autre';
//                case 8:
//                    $result[] = 'Emploi saisonnier';
//                default:
//                    return '';
//            }
//
//            return join(',', $result);
//        }
//
//    }


    public function __toString()
    {
        return $this->value;
    }

    public static function getTypeList(): array
    {
        return array(
            Dictionnaire::TYPE_CONTRACT => 'contract',
            Dictionnaire::TYPE_DIPLOMA => 'diploma',
            Dictionnaire::TYPE_EXPERIENCE => 'experience',
            Dictionnaire::TYPE_LANGUAGE => 'language',
            Dictionnaire::TYPE_LEVEL => 'level',
            Dictionnaire::TYPE_REGION => 'region',
            Dictionnaire::TYPE_CONTRACT_FORMATION => 'contract formation',
            Dictionnaire::TYPE_SOURCE => 'source',
            Dictionnaire::TYPE_START => 'debut',
            Dictionnaire::TYPE_BUDGET => 'budget',
            Dictionnaire::TYPE_SECTEUR => 'secteur',
            Dictionnaire::TYPE_DURATION  => 'duration',
            Dictionnaire::TYPE_REFUS  => 'refus',
            Dictionnaire::TYPE_METIER  => 'Categorie Metier',
            Dictionnaire::TYPE_ENTITE  => 'Entit??',
            Dictionnaire::TYPE_SOL  => 'Couleur du sol',
            Dictionnaire::TYPE_PERSONNAGE  => 'Personnage',
            Dictionnaire::TYPE_COMPTOIRE  => 'Comptoire d???accueil',
            Dictionnaire::TYPE_MOBILIER  => 'Mobilier',
            Dictionnaire::TYPE_PLANTE  => 'Plante',
            Dictionnaire::TYPE_TRANSPORT  => 'Transport',
            Dictionnaire::TYPE_ENTRETIEN  => 'Entretien',
            Dictionnaire::TYPE_FORMATION  => 'Formation0',
            Dictionnaire::TYPE_FORMATION1  => 'Formation1',
            Dictionnaire::TYPE_FORMATION2  => 'Formation2',
            Dictionnaire::TYPE_FORMATION3  => 'Formation3',
            Dictionnaire::TYPE_FORMATION4  => 'Formation4',
            Dictionnaire::TYPE_FORMATION5  => 'Formation5',
            Dictionnaire::TYPE_CATEGORIE_ANNUAIRE  => 'categorie_annuaire',
            Dictionnaire::TYPE_CATEGORIE_AGENDA  => 'categorie_agenda',
            Dictionnaire::TYPE_CATEGORIE_BLOG  => 'categorie_blog',
            Dictionnaire::TYPE_CATEGORIE_CIVILITE  => 'Civilite',
        );
    }

    /**
     * @return Collection
     */
    public function getProfiles(): Collection
    {
        return $this->profile;
    }

    public function addProfile(Profile $profile): self
    {
        if (!$this->profile->contains($profile)) {
            $this->profile[] = $profile;
            $profile->setCivilite($this);
        }

        return $this;
    }

    public function removeProfile(Profile $profile): self
    {
        if ($this->profile->removeElement($profile)) {
            // set the owning side to null (unless already changed)
            if ($profile->getCivilite() === $this) {
                $profile->setCivilite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBlog(): Collection
    {
        return $this->blog;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blog->contains($blog)) {
            $this->blog[] = $blog;
            $blog->addCategorie($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        if ($this->blog->removeElement($blog)) {
            $blog->removeCategorie($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEntreprise(): Collection
    {
        return $this->entreprise;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprise->contains($entreprise)) {
            $this->entreprise[] = $entreprise;
            $entreprise->setSecteur($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprise->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getSecteur() === $this) {
                $entreprise->setSecteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAgenda(): Collection
    {
        return $this->agenda;
    }

    public function addAgenda(Agenda $agenda): self
    {
        if (!$this->agenda->contains($agenda)) {
            $this->agenda[] = $agenda;
            $agenda->setCategorie($this);
        }

        return $this;
    }

    public function removeAgenda(Agenda $agenda): self
    {
        if ($this->agenda->removeElement($agenda)) {
            // set the owning side to null (unless already changed)
            if ($agenda->getCategorie() === $this) {
                $agenda->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMetiers(): Collection
    {
        return $this->metiers;
    }

    public function addMetier(Profile $metiers): self
    {
        if (!$this->metiers->contains($metiers)) {
            $this->metiers[] = $metiers;
            $metiers->setMetiers($this);
        }

        return $this;
    }

    public function removeMetier(Profile $metiers): self
    {
        if ($this->metiers->removeElement($metiers)) {
            // set the owning side to null (unless already changed)
            if ($metiers->getMetiers() === $this) {
                $metiers->setMetiers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDiplomes(): Collection
    {
        return $this->diplome;
    }

    public function addDiplome(Profile $diplome): self
    {
        if (!$this->diplome->contains($diplome)) {
            $this->diplome[] = $diplome;
            $diplome->setDiplome($this);
        }

        return $this;
    }

    public function removeDiplome(Profile $diplome): self
    {
        if ($this->diplome->removeElement($diplome)) {
            // set the owning side to null (unless already changed)
            if ($diplome->getDiplome() === $this) {
                $diplome->setDiplome(null);
            }
        }

        return $this;
    }

  /**
   * @return Collection
   */
  public function getExperiences(): Collection
  {
    return $this->experiences;
  }

  public function addExperience(Profile $experience): self
  {
    if (!$this->experiences->contains($experience)) {
      $this->experiences[] = $experience;
      $experience->setExperiences($this);
    }

    return $this;
  }

  public function removeExperience(Profile $experience): self
  {
    if ($this->experiences->removeElement($experience)) {
      // set the owning side to null (unless already changed)
      if ($experience->getExperiences() === $this) {
        $experience->setExperiences(null);
      }
    }

    return $this;
  }

    /**
     * @return Collection
     */
  public function getAnnuaires(): Collection
  {
      return $this->annuaires;
  }

  public function addAnnuaire(Annuaire $annuaire): self
  {
      if (!$this->annuaires->contains($annuaire)) {
          $this->annuaires[] = $annuaire;
          $annuaire->addCategorie($this);
      }

      return $this;
  }

  public function removeAnnuaire(Annuaire $annuaire): self
  {
      if ($this->annuaires->removeElement($annuaire)) {
          $annuaire->removeCategorie($this);
      }

      return $this;
  }
}
