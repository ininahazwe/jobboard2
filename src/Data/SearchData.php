<?php

namespace App\Data;

use App\Entity\Entreprise;

class SearchData
{
    /**
     * @var int
     */
    public int $page =1;

    /**
     * @var string
     */
    public string $q = '';

    /**
     * @var Entreprise[]
     */
    public array $entreprises = [];
}