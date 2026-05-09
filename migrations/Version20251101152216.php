<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251101152216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE colis (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, warehouse_origine_id INT DEFAULT NULL, warehouse_destination_id INT DEFAULT NULL, numero_colis VARCHAR(50) NOT NULL, poids NUMERIC(10, 2) NOT NULL, description LONGTEXT DEFAULT NULL, pays_origine VARCHAR(50) NOT NULL, pays_destination VARCHAR(50) NOT NULL, images JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', expediteur_nom VARCHAR(100) NOT NULL, expediteur_tel VARCHAR(20) NOT NULL, expediteur_email VARCHAR(180) DEFAULT NULL, destinataire_nom VARCHAR(100) NOT NULL, destinataire_tel VARCHAR(20) NOT NULL, destinataire_email VARCHAR(180) NOT NULL, destinataire_adresse LONGTEXT DEFAULT NULL, statut_actuel VARCHAR(50) NOT NULL, date_enregistrement DATETIME NOT NULL, date_depart DATETIME DEFAULT NULL, date_livraison DATETIME DEFAULT NULL, date_livraison_estimee DATETIME DEFAULT NULL, montant_frais NUMERIC(10, 2) NOT NULL, statut_paiement VARCHAR(20) NOT NULL, INDEX IDX_470BDFF9A76ED395 (user_id), INDEX IDX_470BDFF95B66A2DD (warehouse_origine_id), INDEX IDX_470BDFF9CB3681AC (warehouse_destination_id), UNIQUE INDEX UNIQ_NUMERO_COLIS (numero_colis), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historique_statuts (id INT AUTO_INCREMENT NOT NULL, colis_id INT NOT NULL, user_modificateur_id INT DEFAULT NULL, statut VARCHAR(50) NOT NULL, date_changement DATETIME NOT NULL, commentaire LONGTEXT DEFAULT NULL, INDEX IDX_22215A994D268D70 (colis_id), INDEX IDX_22215A991FE19AD7 (user_modificateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarifs (id INT AUTO_INCREMENT NOT NULL, route VARCHAR(100) NOT NULL, poids_min NUMERIC(10, 2) DEFAULT NULL, poids_max NUMERIC(10, 2) DEFAULT NULL, prix_par_kg NUMERIC(10, 2) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, actif TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, date_creation DATETIME NOT NULL, date_derniere_connexion DATETIME DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649450FF010 (telephone), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, responsable_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, pays VARCHAR(100) NOT NULL, adresse VARCHAR(150) NOT NULL, ville VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, horaires_ouverture LONGTEXT DEFAULT NULL, capacite_maximale NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_ECB38BFC53C59D72 (responsable_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF95B66A2DD FOREIGN KEY (warehouse_origine_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF9CB3681AC FOREIGN KEY (warehouse_destination_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE historique_statuts ADD CONSTRAINT FK_22215A994D268D70 FOREIGN KEY (colis_id) REFERENCES colis (id)');
        $this->addSql('ALTER TABLE historique_statuts ADD CONSTRAINT FK_22215A991FE19AD7 FOREIGN KEY (user_modificateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFC53C59D72 FOREIGN KEY (responsable_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF9A76ED395');
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF95B66A2DD');
        $this->addSql('ALTER TABLE colis DROP FOREIGN KEY FK_470BDFF9CB3681AC');
        $this->addSql('ALTER TABLE historique_statuts DROP FOREIGN KEY FK_22215A994D268D70');
        $this->addSql('ALTER TABLE historique_statuts DROP FOREIGN KEY FK_22215A991FE19AD7');
        $this->addSql('ALTER TABLE warehouse DROP FOREIGN KEY FK_ECB38BFC53C59D72');
        $this->addSql('DROP TABLE colis');
        $this->addSql('DROP TABLE historique_statuts');
        $this->addSql('DROP TABLE tarifs');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
