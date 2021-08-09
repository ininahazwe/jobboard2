<?php

namespace App\Tests\Entity;

use App\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnnonceTest extends KernelTestCase
{
    public function getEntity(): Annonce
    {
        return (new Annonce())
            ->setName('mon annonce')
            ->setDescription('ceci est une description')
            ->setIsActive(true)
            ->setReference('annonce_1');
    }

    public function assertHasErrors(Annonce $annonce, int $number = 0)
    {
        self::bootKernel();
        $error = self::$container->get('validator')->validate($annonce);
        $this->assertCount($number, $error);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidAnnonceEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName('12'), 1);
    }

    public function testInvalidBlankNameEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName(''),1);
    }

    public function testInvalidBlankDescriptionEntity()
    {
        $this->assertHasErrors($this->getEntity()->setName(''),1);
    }

    public function testInvalidUsedName()
    {
        $this->assertHasErrors($this->getEntity()->setName('informaticien'), 1);
    }
}