<?php

namespace App\Tests;

use App\Entity\Annonce;
use PHPUnit\Framework\TestCase;

class AnnonceUnitTest extends TestCase
{
    public function testIsTrue()
    {
        $annonce = new Annonce();

        $annonce->setName('Mon annonce')
                ->setDescription('Ceci est une annonce')
                ->setIsActive(true)
                ->setReference('annonce')
            ;

        $this->assertTrue($annonce->setName() == 'Mon annonce');
        $this->assertTrue($annonce->setDescription() == 'Ceci est une annonce');
        $this->assertTrue($annonce->setIsActive() == true);
        $this->assertTrue($annonce->setReference() == 'annonce');
    }

    public function testIsFalse()
    {
        $annonce = new Annonce();

        $annonce->setName('Mon annonce')
            ->setDescription('Ceci est une annonce')
            ->setIsActive(false)
            ->setReference('annonce')
        ;

        $this->assertFalse($annonce->getName() === 'false');
        $this->assertFalse($annonce->getDescription() === 'false');
        $this->assertFalse($annonce->getIsActive() === false);
        $this->assertFalse($annonce->getReference() === 'false');
    }

    public function testIsEmpty()
    {
        $annonce = new Annonce();

        $this->assertEmpty($annonce->getName());
        $this->assertEmpty($annonce->getDescription());
        $this->assertEmpty($annonce->getIsActive());
        $this->assertEmpty($annonce->getReference());
    }
}
