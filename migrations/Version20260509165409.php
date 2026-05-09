<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509165409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE colis (id SERIAL NOT NULL, user_id INT DEFAULT NULL, warehouse_origine_id INT DEFAULT NULL, warehouse_destination_id INT DEFAULT NULL, numero_colis VARCHAR(50) NOT NULL, poids NUMERIC(10, 2) NOT NULL, description TEXT DEFAULT NULL, pays_origine VARCHAR(50) NOT NULL, pays_destination VARCHAR(50) NOT NULL, images JSON DEFAULT NULL, expediteur_nom VARCHAR(100) NOT NULL, expediteur_tel VARCHAR(20) NOT NULL, expediteur_email VARCHAR(180) DEFAULT NULL, destinataire_nom VARCHAR(100) NOT NULL, destinataire_tel VARCHAR(20) NOT NULL, destinataire_email VARCHAR(180) NOT NULL, destinataire_adresse TEXT DEFAULT NULL, statut_actuel VARCHAR(50) NOT NULL, date_enregistrement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_depart TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_livraison TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_livraison_estimee TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, montant_frais NUMERIC(10, 2) NOT NULL, statut_paiement VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_470BDFF9A76ED395 ON colis (user_id)');
        $this->addSql('CREATE INDEX IDX_470BDFF95B66A2DD ON colis (warehouse_origine_id)');
        $this->addSql('CREATE INDEX IDX_470BDFF9CB3681AC ON colis (warehouse_destination_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_NUMERO_COLIS ON colis (numero_colis)');
        $this->addSql('CREATE TABLE historique_statuts (id SERIAL NOT NULL, colis_id INT NOT NULL, user_modificateur_id INT DEFAULT NULL, statut VARCHAR(50) NOT NULL, date_changement TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22215A994D268D70 ON historique_statuts (colis_id)');
        $this->addSql('CREATE INDEX IDX_22215A991FE19AD7 ON historique_statuts (user_modificateur_id)');
        $this->addSql('CREATE TABLE tarifs (id SERIAL NOT NULL, route VARCHAR(100) NOT NULL, poids_min NUMERIC(10, 2) DEFAULT NULL, poids_max NUMERIC(10, 2) DEFAULT NULL, prix_par_kg NUMERIC(10, 2) NOT NULL, date_debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_fin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, description TEXT DEFAULT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(20) NOT NULL, date_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_derniere_connexion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649450FF010 ON "user" (telephone)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE warehouse (id SERIAL NOT NULL, responsable_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, pays VARCHAR(100) NOT NULL, adresse VARCHAR(150) NOT NULL, ville VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, horaires_ouverture TEXT DEFAULT NULL, capacite_maximale NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ECB38BFC53C59D72 ON warehouse (responsable_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF95B66A2DD FOREIGN KEY (warehouse_origine_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE colis ADD CONSTRAINT FK_470BDFF9CB3681AC FOREIGN KEY (warehouse_destination_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historique_statuts ADD CONSTRAINT FK_22215A994D268D70 FOREIGN KEY (colis_id) REFERENCES colis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE historique_statuts ADD CONSTRAINT FK_22215A991FE19AD7 FOREIGN KEY (user_modificateur_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE warehouse ADD CONSTRAINT FK_ECB38BFC53C59D72 FOREIGN KEY (responsable_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE colis DROP CONSTRAINT FK_470BDFF9A76ED395');
        $this->addSql('ALTER TABLE colis DROP CONSTRAINT FK_470BDFF95B66A2DD');
        $this->addSql('ALTER TABLE colis DROP CONSTRAINT FK_470BDFF9CB3681AC');
        $this->addSql('ALTER TABLE historique_statuts DROP CONSTRAINT FK_22215A994D268D70');
        $this->addSql('ALTER TABLE historique_statuts DROP CONSTRAINT FK_22215A991FE19AD7');
        $this->addSql('ALTER TABLE warehouse DROP CONSTRAINT FK_ECB38BFC53C59D72');
        $this->addSql('DROP TABLE colis');
        $this->addSql('DROP TABLE historique_statuts');
        $this->addSql('DROP TABLE tarifs');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
