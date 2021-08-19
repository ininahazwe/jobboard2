<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Contact
{
    use ResourceId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $subject;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $message;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}