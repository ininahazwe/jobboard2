<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210630081915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annuaire ADD categorie_id INT DEFAULT NULL, ADD telephone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE annuaire ADD CONSTRAINT FK_456BA70BBCF5E72D FOREIGN KEY (categorie_id) REFERENCES dictionnaire (id)');
        $this->addSql('CREATE INDEX IDX_456BA70BBCF5E72D ON annuaire (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annuaire DROP FOREIGN KEY FK_456BA70BBCF5E72D');
        $this->addSql('DROP INDEX IDX_456BA70BBCF5E72D ON annuaire');
        $this->addSql('ALTER TABLE annuaire DROP categorie_id, DROP telephone');
    }
}
