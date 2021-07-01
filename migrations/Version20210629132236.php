<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629132236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file ADD annuaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F36105132B86A FOREIGN KEY (annuaire_id) REFERENCES annuaire (id)');
        $this->addSql('CREATE INDEX IDX_8C9F36105132B86A ON file (annuaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F36105132B86A');
        $this->addSql('DROP INDEX IDX_8C9F36105132B86A ON file');
        $this->addSql('ALTER TABLE file DROP annuaire_id');
    }
}
