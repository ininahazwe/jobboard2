<?php

namespace App\Data;

use App\Entity\Annonce;
use App\Entity\Dictionnaire;
use App\Entity\Entreprise;

class SearchDataAnnonces
{
    /**
     * @var int
     */
    public int $page = 1;

    /**
     * @var string
     */
    public string $q;

    /**
     * @var Annonce[]
     */
    public array $annonces = [];

    /**
     * @var Entreprise[]
     */
    public array $entreprises = [];

    /**
     * @var Dictionnaire[]
     */
    public array $contrat = [];

    /**
     * @var Dictionnaire[]
     */
    public array $diplome = [];

    /**
     * @var Dictionnaire[]
     */
    public array $experience = [];

}