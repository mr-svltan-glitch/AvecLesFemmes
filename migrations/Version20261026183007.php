<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026183007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES membre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, date_created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, type_campagne_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_539B5D165D3BA4E5 FOREIGN KEY (type_campagne_id) REFERENCES type_campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_539B5D165D3BA4E5 ON campagne (type_campagne_id)');
        $this->addSql('CREATE TABLE cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_paiement DATETIME DEFAULT NULL, statut VARCHAR(50) NOT NULL, mode_cotisation_id INTEGER NOT NULL, seance_id INTEGER NOT NULL, CONSTRAINT FK_AE64D2ED1A942B1D FOREIGN KEY (mode_cotisation_id) REFERENCES mode_cotisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AE64D2EDE3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_AE64D2ED1A942B1D ON cotisation (mode_cotisation_id)');
        $this->addSql('CREATE INDEX IDX_AE64D2EDE3797A94 ON cotisation (seance_id)');
        $this->addSql('CREATE TABLE epargne (id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_A19A894CBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE groupement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE membre (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, telephone VARCHAR(30) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, login VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, groupement_id INTEGER NOT NULL, role_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_F6B4FB29E66695CE FOREIGN KEY (groupement_id) REFERENCES groupement (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F6B4FB29D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F6B4FB29E66695CE ON membre (groupement_id)');
        $this->addSql('CREATE INDEX IDX_F6B4FB29D60322AC ON membre (role_id)');
        $this->addSql('CREATE TABLE membre_campagne (membre_id INTEGER NOT NULL, campagne_id INTEGER NOT NULL, PRIMARY KEY (membre_id, campagne_id), CONSTRAINT FK_E6F9B5256A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E6F9B52516227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E6F9B5256A99F74A ON membre_campagne (membre_id)');
        $this->addSql('CREATE INDEX IDX_E6F9B52516227374 ON membre_campagne (campagne_id)');
        $this->addSql('CREATE TABLE mode_cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, is_libre BOOLEAN NOT NULL, membre_id INTEGER NOT NULL, campagne_id INTEGER NOT NULL, CONSTRAINT FK_6BB9377E6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6BB9377E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6BB9377E6A99F74A ON mode_cotisation (membre_id)');
        $this->addSql('CREATE INDEX IDX_6BB9377E16227374 ON mode_cotisation (campagne_id)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE seance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_seance DATE NOT NULL, frequence VARCHAR(100) NOT NULL, statut VARCHAR(50) NOT NULL, campagne_id INTEGER NOT NULL, CONSTRAINT FK_DF7DFD0E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DF7DFD0E16227374 ON seance (campagne_id)');
        $this->addSql('CREATE TABLE tontine (id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_3F164B7FBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE TABLE type_campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description CLOB DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE campagne');
        $this->addSql('DROP TABLE cotisation');
        $this->addSql('DROP TABLE epargne');
        $this->addSql('DROP TABLE groupement');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE membre_campagne');
        $this->addSql('DROP TABLE mode_cotisation');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE tontine');
        $this->addSql('DROP TABLE type_campagne');
    }
}
