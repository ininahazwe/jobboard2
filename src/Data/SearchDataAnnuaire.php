<?php

namespace App\Data;

use App\Entity\Adresse;
use App\Entity\Annuaire;
use App\Entity\Dictionnaire;

class SearchDataAnnuaire
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
     * @var Annuaire[]
     */
    public array $annuaire = [];

    /**
     * @var Dictionnaire[]
     */
    public array $category = [];

    /**
     * @var Adresse[]
     */
    public array $adresse = [];

    /**
     * @var Adresse[]
     */
    public array $departement = [];
}