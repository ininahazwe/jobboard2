<?php

namespace App\Data;

use App\Entity\Blog;
use App\Entity\Dictionnaire;

class SearchDataBlog
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
     * @var Blog[]
     */
    public array $blogs = [];

    /**
     * @var Dictionnaire[]
     */
    public array $category = [];

}