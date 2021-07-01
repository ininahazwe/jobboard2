<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210621092310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT DEFAULT NULL, created_at DATETIME NOT NULL, prix VARCHAR(255) NOT NULL, tva VARCHAR(255) NOT NULL, prix_ttc VARCHAR(255) NOT NULL, is_paid TINYINT(1) NOT NULL, limite_date_paid DATETIME DEFAULT NULL, payment_date DATETIME DEFAULT NULL, payment_methods INT DEFAULT NULL, INDEX IDX_FE866410A4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE offre ADD facture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE INDEX IDX_AF86866F7F2DEE08 ON offre (facture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F7F2DEE08');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP INDEX IDX_AF86866F7F2DEE08 ON offre');
        $this->addSql('ALTER TABLE offre DROP facture_id');
    }
}
