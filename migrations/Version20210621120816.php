<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621120816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise ADD ref_client VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE facture ADD reference VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE prix prix VARCHAR(255) DEFAULT NULL, CHANGE tva tva VARCHAR(255) DEFAULT NULL, CHANGE prix_ttc prix_ttc VARCHAR(255) DEFAULT NULL, CHANGE is_paid is_paid TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise DROP ref_client');
        $this->addSql('ALTER TABLE facture DROP reference, CHANGE created_at created_at DATETIME NOT NULL, CHANGE prix prix VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE tva tva VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE prix_ttc prix_ttc VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE is_paid is_paid TINYINT(1) NOT NULL');
    }
}
