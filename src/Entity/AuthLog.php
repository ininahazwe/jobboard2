<?php

namespace App\Entity;

use App\Repository\AuthLogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AuthLogRepository::class)
 * @ORM\Table(name="auth_logs")
 */
class AuthLog
{
    use ResourceId;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $authAttemptAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $userIP;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $emailEntered;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isSuccessfulAuth;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $startOfBlackListing;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $endOfBlackListing;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isRememberMeAuth;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $deautheticatedAt;

    public function __construct(string $emailEntered, ?string $userIP)
    {
        $this->authAttemptAt = new \DateTimeImmutable('now');
        $this->emailEntered = $emailEntered;
        $this->isRememberMeAuth = false;
        $this->userIP = $userIP;
    }

    public function getAuthAttemptAt(): ?\DateTimeImmutable
    {
        return $this->authAttemptAt;
    }

    public function setAuthAttemptAt(\DateTimeImmutable $authAttemptAt): self
    {
        $this->authAttemptAt = $authAttemptAt;

        return $this;
    }

    public function getUserIP(): ?string
    {
        return $this->userIP;
    }

    public function setUserIP(?string $userIP): self
    {
        $this->userIP = $userIP;

        return $this;
    }

    public function getEmailEntered(): ?string
    {
        return $this->emailEntered;
    }

    public function setEmailEntered(string $emailEntered): self
    {
        $this->emailEntered = $emailEntered;

        return $this;
    }

    public function getIsSuccessfulAuth(): ?bool
    {
        return $this->isSuccessfulAuth;
    }

    public function setIsSuccessfulAuth(bool $isSuccessfulAuth): self
    {
        $this->isSuccessfulAuth = $isSuccessfulAuth;

        return $this;
    }

    public function getStartOfBlackListing(): ?\DateTimeImmutable
    {
        return $this->startOfBlackListing;
    }

    public function setStartOfBlackListing(?\DateTimeImmutable $startOfBlackListing): self
    {
        $this->startOfBlackListing = $startOfBlackListing;

        return $this;
    }

    public function getEndOfBlackListing(): ?\DateTimeImmutable
    {
        return $this->endOfBlackListing;
    }

    public function setEndOfBlackListing(?\DateTimeImmutable $endOfBlackListing): self
    {
        $this->endOfBlackListing = $endOfBlackListing;

        return $this;
    }

    public function getIsRememberMeAuth(): ?bool
    {
        return $this->isRememberMeAuth;
    }

    public function setIsRememberMeAuth(bool $isRememberMeAuth): self
    {
        $this->isRememberMeAuth = $isRememberMeAuth;

        return $this;
    }

    public function getDeautheticatedAt(): ?\DateTimeImmutable
    {
        return $this->deautheticatedAt;
    }

    public function setDeautheticatedAt(?\DateTimeImmutable $deautheticatedAt): self
    {
        $this->deautheticatedAt = $deautheticatedAt;

        return $this;
    }
}
