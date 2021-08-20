<?php

namespace App\Entity;

use App\Repository\DepartementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepartementsRepository::class)
 */
class Villes
{
    use ResourceId;

    /**
     * @ORM\Column(type="string", length=255)
     */

    private ?string $name;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Villes
     */
    public function setName(?string $name): Villes
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return Villes
     */
    public function setCode(?string $code): Villes
    {
        $this->code = $code;
        return $this;
    }
    /**
     * @ORM\Column(type="string", length=3)
     */
    private ?string $code;

    public function _toString(): string
    {
        return $this->name ?: '';
    }


}