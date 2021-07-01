<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210623122319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonces_contrat (annonce_id INT NOT NULL, dictionnaire_id INT NOT NULL, INDEX IDX_3DF1079B8805AB2F (annonce_id), INDEX IDX_3DF1079BE70AF195 (dictionnaire_id), PRIMARY KEY(annonce_id, dictionnaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annonces_auteurs (annonce_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_30124A228805AB2F (annonce_id), INDEX IDX_30124A22A76ED395 (user_id), PRIMARY KEY(annonce_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annonces_location (annonce_id INT NOT NULL, dictionnaire_id INT NOT NULL, INDEX IDX_5018C4678805AB2F (annonce_id), INDEX IDX_5018C467E70AF195 (dictionnaire_id), PRIMARY KEY(annonce_id, dictionnaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonces_contrat ADD CONSTRAINT FK_3DF1079B8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE annonces_contrat ADD CONSTRAINT FK_3DF1079BE70AF195 FOREIGN KEY (dictionnaire_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE annonces_auteurs ADD CONSTRAINT FK_30124A228805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE annonces_auteurs ADD CONSTRAINT FK_30124A22A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE annonces_location ADD CONSTRAINT FK_5018C4678805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE annonces_location ADD CONSTRAINT FK_5018C467E70AF195 FOREIGN KEY (dictionnaire_id) REFERENCES dictionnaire (id)');
        $this->addSql('DROP INDEX IDX_F65593E5F675F31B ON annonce');
        $this->addSql('ALTER TABLE annonce ADD diplome_id INT DEFAULT NULL, ADD experience_id INT DEFAULT NULL, ADD entreprise_id INT DEFAULT NULL, ADD reference VARCHAR(255) DEFAULT NULL, ADD lien VARCHAR(255) DEFAULT NULL, ADD adresse_email VARCHAR(255) DEFAULT NULL, DROP author_id');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E526F859E2 FOREIGN KEY (diplome_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E546E90E27 FOREIGN KEY (experience_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_F65593E526F859E2 ON annonce (diplome_id)');
        $this->addSql('CREATE INDEX IDX_F65593E546E90E27 ON annonce (experience_id)');
        $this->addSql('CREATE INDEX IDX_F65593E5A4AEAFEA ON annonce (entreprise_id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610E70AF195 FOREIGN KEY (dictionnaire_id) REFERENCES dictionnaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE annonces_contrat');
        $this->addSql('DROP TABLE annonces_auteurs');
        $this->addSql('DROP TABLE annonces_location');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E526F859E2');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E546E90E27');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5A4AEAFEA');
        $this->addSql('DROP INDEX IDX_F65593E526F859E2 ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E546E90E27 ON annonce');
        $this->addSql('DROP INDEX IDX_F65593E5A4AEAFEA ON annonce');
        $this->addSql('ALTER TABLE annonce ADD author_id INT NOT NULL, DROP diplome_id, DROP experience_id, DROP entreprise_id, DROP reference, DROP lien, DROP adresse_email');
        $this->addSql('CREATE INDEX IDX_F65593E5F675F31B ON annonce (author_id)');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610E70AF195');
    }
}
