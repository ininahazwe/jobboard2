<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Impossible de créer un compte utilisateur avec cet email")
 */
class User implements UserInterface
{
    const EN_ATTENTE = 0;
    const ACCEPTEE = 1;
    const REFUSEE = 2;

    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     * @var array<string>
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Unique
     */
    protected string $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Ton prénom ne doit pas contenir de chiffre"
     * )
     * @Assert\Length(min="3", max="15", minMessage = "un minimum de {{ limit }} caractères est requis", maxMessage = "{{ limit }} caractères sont la limite")
     */
    private string $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Ton nom ne doit pas contenir de chiffre"
     * )
     * @Assert\Length(min="3", max="15", minMessage = "un minimum de {{ limit }} caractères est requis", maxMessage = "{{ limit }} caractères sont la limite")
     */
    private string $lastname;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default": "CURRENT_TIMESTAMP"})
     */
    protected ?\DateTime $lastConnexionAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern = "/^[0-9]+$/i",
     *     message = "Seul les chiffres sont acceptés",
     * )
     */
    private ?string $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isTermsClients;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Profile $profile;

    /**
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="avatar")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?File $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $forgotPasswordToken;

    private ?DateTimeImmutable $lastConnextionAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenRequestedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $forgotPasswordTokenVerifiedAt;

    /**
     * @ORM\OneToMany(targetEntity=Candidature::class, mappedBy="candidat")
     */
    private Collection $candidatures;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $activation_token;

    /**
     * @ORM\ManyToMany(targetEntity=Entreprise::class, mappedBy="recruteurs")
     */
    private Collection $entreprises;

    /**
     * @ORM\ManyToMany(targetEntity=Entreprise::class, mappedBy="super_recruteurs")
     */
    private Collection $recruteurs_entreprise;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @OrderBy({"id" = "ASC"})
     */
    private Collection $files;

    /**
     * @ORM\ManyToMany(targetEntity=Annonce::class, mappedBy="auteur")
     */
    private Collection $annonces_auteurs;

    /**
     * @ORM\OneToMany(targetEntity=Candidature::class, mappedBy="recruteur")
     */
    private Collection $candidatures_recruteur;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="sender", orphanRemoval=true)
     */
    private Collection $sent;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="recipient", orphanRemoval=true)
     */
    private Collection $received;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="current_recruteur")
     */
    private Collection $annonces;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $moderation;

    /**
     * @ORM\OneToMany(targetEntity=Blog::class, mappedBy="author")
     */
    private Collection $blogs;

    /**
     * @ORM\ManyToMany(targetEntity=Annonce::class, mappedBy="favoris")
     */
    private Collection $favoris;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isAccepted;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private Collection $orders;

    public function __construct()
    {
        $this->isTermsClients = false;
        $this->roles = ['ROLE_CANDIDAT'];
        $this->candidatures = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
        $this->recruteurs_entreprise = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->annonces_auteurs = new ArrayCollection();
        $this->candidatures_recruteur = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->received = new ArrayCollection();
        $this->annonces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->blogs = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CANDIDAT';

        return array_unique($roles);
    }

    /**
     * set the user role
     *
     * @param array<string> $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastConnexionAt(): ?\DateTimeInterface
    {
        return $this->lastConnextionAt;
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

    public function getIsTermsClients(): ?bool
    {
        return $this->isTermsClients;
    }

    public function setIsTermsClients(bool $isTermsClients): self
    {
        $this->isTermsClients = $isTermsClients;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        // unset the owning side of the relation if necessary
        if ($profile === null && $this->profile !== null) {
            $this->profile->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($profile !== null && $profile->getUser() !== $this) {
            $profile->setUser($this);
        }

        $this->profile = $profile;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    /**
     * @param File|null $avatar
     * @return User
     */
    public function setAvatar(?File $avatar): User
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getForgotPasswordToken(): ?string
    {
        return $this->forgotPasswordToken;
    }

    /**
     * @param string|null $forgotPasswordToken
     */
    public function setForgotPasswordToken(?string $forgotPasswordToken): void
    {
        $this->forgotPasswordToken = $forgotPasswordToken;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getForgotPasswordTokenRequestedAt(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenRequestedAt;
    }

    /**
     * @param DateTimeImmutable|null $forgotPasswordTokenRequestedAt
     */
    public function setForgotPasswordTokenRequestedAt(?DateTimeImmutable $forgotPasswordTokenRequestedAt): void
    {
        $this->forgotPasswordTokenRequestedAt = $forgotPasswordTokenRequestedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getForgotPasswordTokenMustBeVerifiedBefore(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenMustBeVerifiedBefore;
    }

    /**
     * @param DateTimeImmutable|null $forgotPasswordTokenMustBeVerifiedBefore
     */
    public function setForgotPasswordTokenMustBeVerifiedBefore(?DateTimeImmutable $forgotPasswordTokenMustBeVerifiedBefore): void
    {
        $this->forgotPasswordTokenMustBeVerifiedBefore = $forgotPasswordTokenMustBeVerifiedBefore;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getForgotPasswordTokenVerifiedAt(): ?DateTimeImmutable
    {
        return $this->forgotPasswordTokenVerifiedAt;
    }

    /**
     * @param DateTimeImmutable|null $forgotPasswordTokenVerifiedAt
     */
    public function setForgotPasswordTokenVerifiedAt(?DateTimeImmutable $forgotPasswordTokenVerifiedAt): void
    {
        $this->forgotPasswordTokenVerifiedAt = $forgotPasswordTokenVerifiedAt;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function getOneRecruteurEntreprise(): string
    {
        $result = array();
        foreach($this->getEntreprises() as $entreprise){
            $result[] ="<b>Recruteur</b>" . ' ' . $entreprise->getName() . '<br/>';
        }
        foreach($this->getRecruteursEntreprise() as $entreprise){
            $result[] ="<b>Super Recruteur</b>" . ' ' . $entreprise->getName() . '<br/>';
        }

        return $this->getEmail() . "<br/><br>" . implode('<br/>', $result);
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
            $candidature->setCandidat($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getCandidat() === $this) {
                $candidature->setCandidat(null);
            }
        }

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises[] = $entreprise;
            $entreprise->addRecruteur($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            $entreprise->removeRecruteur($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRecruteursEntreprise(): Collection
    {
        return $this->recruteurs_entreprise;
    }

    public function addRecruteursEntreprise(Entreprise $recruteursEntreprise): self
    {
        if (!$this->recruteurs_entreprise->contains($recruteursEntreprise)) {
            $this->recruteurs_entreprise[] = $recruteursEntreprise;
            $recruteursEntreprise->addSuperRecruteur($this);
        }

        return $this;
    }

    public function removeRecruteursEntreprise(Entreprise $recruteursEntreprise): self
    {
        if ($this->recruteurs_entreprise->removeElement($recruteursEntreprise)) {
            $recruteursEntreprise->removeSuperRecruteur($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getUser() === $this) {
                $file->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCandidaturesRecruteur(): Collection
    {
        return $this->candidatures_recruteur;
    }

    public function addCandidaturesRecruteur(Candidature $candidaturesRecruteur): self
    {
        if (!$this->candidatures_recruteur->contains($candidaturesRecruteur)) {
            $this->candidatures_recruteur[] = $candidaturesRecruteur;
            $candidaturesRecruteur->setRecruteur($this);
        }

        return $this;
    }

    public function removeCandidaturesRecruteur(Candidature $candidaturesRecruteur): self
    {
        if ($this->candidatures_recruteur->removeElement($candidaturesRecruteur)) {
            // set the owning side to null (unless already changed)
            if ($candidaturesRecruteur->getRecruteur() === $this) {
                $candidaturesRecruteur->setRecruteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Messages $received): self
    {
        if (!$this->received->contains($received)) {
            $this->received[] = $received;
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Messages $received): self
    {
        if ($this->received->removeElement($received)) {
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getUrlLastFile(): string
    {
        $url = "";
        $files = array();
        foreach ($this->getFiles() as $file){
          if($file->getType() == File::TYPE_AVATAR) {
            $files[] = $file->getName();
          }
        }

        $file = end($files);
        $url = "uploads/" . $file;
        return $url ;
    }

    /**
     * @return Collection
     */
    public function getFile(): Collection
    {
        return $this->files;
    }

    public function addFiles(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFiles(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getEntreprise() === $this) {
                $file->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAnnoncesAuteurs(): Collection
    {
        return $this->annonces_auteurs;
    }

    public function addAnnoncesAuteur(Annonce $annoncesAuteur): self
    {
        if (!$this->annonces_auteurs->contains($annoncesAuteur)) {
            $this->annonces_auteurs[] = $annoncesAuteur;
            $annoncesAuteur->addAuteur($this);
        }

        return $this;
    }

    public function removeAnnoncesAuteur(Annonce $annoncesAuteur): self
    {
        if ($this->annonces_auteurs->removeElement($annoncesAuteur)) {
            $annoncesAuteur->removeAuteur($this);
        }

        return $this;
    }

    public function getEntrepriseIds(): array
    {

        $ids = array();
        foreach($this->getRecruteursEntreprise() as $entreprise){
            if (!in_array($entreprise->getId(), $ids)){
                $ids[$entreprise->getId()] = $entreprise->getId();
            }
        }
        foreach($this->getEntreprises() as $entreprise){
            if (!in_array($entreprise->getId(), $ids)){
                $ids[$entreprise->getId()] = $entreprise->getId();
            }
        }
        return $ids;
    }

    public function getEntrepriseAll(): ArrayCollection
    {

        $entreprises= new ArrayCollection();
        foreach($this->getRecruteursEntreprise() as $entreprise){
            if (!$entreprises->contains($entreprise)){
                $entreprises->add($entreprise);
            }
        }
        foreach($this->getEntreprises() as $entreprise){
            if (!$entreprises->contains($entreprise)){
                $entreprises->add($entreprise);
            }
        }
        return $entreprises;
    }

    public function getNbrAnnoncesMaxi(): string
    {
        $nombres = array();
        $entreprises = $this->getEntrepriseAll();

        foreach ($entreprises as $entreprise){
            foreach ($entreprise->getOffres() as $offre){
                $nombre = null;
                if ($offre->isActive()){
                    if ($offre->getNombreOffres() == 0){
                        $nombre = ' illimité ';
                    }else{
                        $nombre = $nombre + $offre->getNombreOffres();
                    }

                }
                $nombres[] = $entreprise->getName() . ' : ' .$nombre;
            }
        }

        return implode(' - ', $nombres);

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
            $annonce->setCurrentRecruteur($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getCurrentRecruteur() === $this) {
                $annonce->setCurrentRecruteur(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */


    public function getRoleName():string
    {
        if ($this->isSuperAdmin()){
            return 'Super Administrateur';
        }else if($this->isSuperRecruteur()){
            return 'Super Recruteur';
        }else if($this->isRecruteur()){
            return 'Recruteur';
        }else if($this->isCandidat()){
            return 'Candidat';
        }else if($this->isCommunicant()){
            return 'Communicant';
        }else{
            return 'Non renseigné';
        }
    }

    public function checkRoles($role): bool
    {
        foreach($this->roles as $item)
        {
            if($item == $role)
            {
                return true;
            }
        }
        return false;
    }

    public function getFullname(): string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function isCandidat(): bool
    {
        $role = "ROLE_CANDIDAT";
        return $this->checkRoles($role);
    }

    public function isRecruteur(): bool
    {
        $role = "ROLE_RECRUTEUR";
        return $this->checkRoles($role);
    }

    public function isSuperRecruteur(): bool
    {
        $role = "ROLE_SUPER_RECRUTEUR";
        return $this->checkRoles($role);
    }

    public function isCommunicant(): bool
    {
        $role = "ROLE_COMMUNICANT";
        return $this->checkRoles($role);
    }

    public function isSuperAdmin(): bool
    {
        $role = "ROLE_SUPER_ADMIN_HANDICV";
        return $this->checkRoles($role);
    }

    public function getModeration(): ?string
    {
        return $this->moderation;
    }

    public function setModeration(?string $moderation): self
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
            return 'Accepté';
        }else if($moderation = '2'){
            return 'Refusé';
        }
    }

    /**
     * @return Collection
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): self
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs[] = $blog;
            $blog->setAuthor($this);
        }

        return $this;
    }

    public function removeBlog(Blog $blog): self
    {
        if ($this->blogs->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getAuthor() === $this) {
                $blog->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Annonce $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->addFavori($this);
        }

        return $this;
    }

    public function removeFavori(Annonce $favori): self
    {
        if ($this->favoris->removeElement($favori)) {
            $favori->removeFavori($this);
        }

        return $this;
    }

    public function getIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

}
