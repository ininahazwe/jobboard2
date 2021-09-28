<?php

namespace App\Data;

use App\Entity\Adresse;
use App\Entity\Agenda;
use App\Entity\Dictionnaire;

class SearchDataAgenda
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
     * @var Agenda[]
     */
    public array $agenda = [];

    /**
     * @var Dictionnaire[]
     */
    public array $category = [];

    /**
     * @var bool
     */
    public bool $virtuel = false;

    /**
     * @var Adresse[]
     */
    public array $adresse = [];
}