<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210618080320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX IDX_D19FA605E237E066DE44026 ON entreprise (name, description)');
        $this->addSql('ALTER TABLE file CHANGE name_file name_file VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE modele_offre_commerciale CHANGE slug slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE FULLTEXT INDEX IDX_140AB6202B36786BFEC530A9 ON page (title, content)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D19FA605E237E066DE44026 ON entreprise');
        $this->addSql('ALTER TABLE file CHANGE name_file name_file VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE modele_offre_commerciale CHANGE slug slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX IDX_140AB6202B36786BFEC530A9 ON page');
    }
}
