<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261026211817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align the SQLite schema with the validated UML mappings while refusing ambiguous existing data.';
    }

    public function up(Schema $schema): void
    {
        $tablesRequiringManualDataMigration = [
            'admin' => 'Admin rows must be checked against the joined-inheritance mapping.',
            'campagne' => 'Campaign rows must be checked before the subtype and type schema changes.',
            'cotisation' => 'Contribution rows require review before enforcing required payment dates.',
            'epargne' => 'Savings rows have no values yet for the new but and benefice fields.',
            'membre' => 'Member rows must be reviewed before enforcing required contact fields.',
            'membre_campagne' => 'Direct member-campaign links cannot be converted without an isLibre value.',
            'mode_cotisation' => 'Contribution-mode rows must be checked before adding pair uniqueness.',
            'role' => 'Role rows cannot be assigned to a groupement automatically.',
            'seance' => 'Session rows cannot be assigned to an admin automatically.',
            'tontine' => 'Tontine rows have no values yet for the new numero and client fields.',
        ];

        foreach ($tablesRequiringManualDataMigration as $table => $reason) {
            $rowCount = (int) $this->connection->fetchOne(sprintf('SELECT COUNT(*) FROM "%s"', $table));
            $this->abortIf(
                $rowCount > 0,
                sprintf('Migration stopped: %s contains %d row(s). %s', $table, $rowCount, $reason),
            );
        }

        $this->abortIf(
            (int) $this->connection->fetchOne('SELECT COUNT(*) FROM type_campagne WHERE description IS NULL') > 0,
            'Migration stopped: type_campagne contains NULL descriptions, which cannot satisfy the required UML field.',
        );
        $this->abortIf(
            $this->connection->fetchAllAssociative('PRAGMA foreign_key_check') !== [],
            'Migration stopped: SQLite reports existing foreign-key violations.',
        );

        $this->addSql('DROP TABLE membre_campagne');
        $this->addSql('CREATE TEMPORARY TABLE __temp__admin AS SELECT id FROM admin');
        $this->addSql('DROP TABLE admin');
        $this->addSql('CREATE TABLE admin (id INTEGER NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES membre (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO admin (id) SELECT id FROM __temp__admin');
        $this->addSql('DROP TABLE __temp__admin');
        $this->addSql('CREATE TEMPORARY TABLE __temp__campagne AS SELECT id, nom, date_debut, montant, date_created_at, updated_at, type_campagne_id, discr FROM campagne');
        $this->addSql('DROP TABLE campagne');
        $this->addSql('CREATE TABLE campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, date_debut DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, type_campagne_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, date_fin DATE DEFAULT NULL, CONSTRAINT FK_539B5D165D3BA4E5 FOREIGN KEY (type_campagne_id) REFERENCES type_campagne (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO campagne (id, nom, date_debut, montant, created_at, updated_at, type_campagne_id, discr) SELECT id, nom, date_debut, montant, date_created_at, updated_at, type_campagne_id, discr FROM __temp__campagne');
        $this->addSql('DROP TABLE __temp__campagne');
        $this->addSql('CREATE INDEX IDX_539B5D165D3BA4E5 ON campagne (type_campagne_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cotisation AS SELECT id, montant, date_paiement, statut, mode_cotisation_id, seance_id FROM cotisation');
        $this->addSql('DROP TABLE cotisation');
        $this->addSql('CREATE TABLE cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_paiement DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, mode_cotisation_id INTEGER NOT NULL, seance_id INTEGER NOT NULL, CONSTRAINT FK_AE64D2ED1A942B1D FOREIGN KEY (mode_cotisation_id) REFERENCES mode_cotisation (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AE64D2EDE3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cotisation (id, montant, date_paiement, statut, mode_cotisation_id, seance_id) SELECT id, montant, date_paiement, statut, mode_cotisation_id, seance_id FROM __temp__cotisation');
        $this->addSql('DROP TABLE __temp__cotisation');
        $this->addSql('CREATE INDEX IDX_AE64D2EDE3797A94 ON cotisation (seance_id)');
        $this->addSql('CREATE INDEX IDX_AE64D2ED1A942B1D ON cotisation (mode_cotisation_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__epargne AS SELECT id FROM epargne');
        $this->addSql('DROP TABLE epargne');
        $this->addSql('CREATE TABLE epargne (id INTEGER NOT NULL, but VARCHAR(255) NOT NULL, benefice DOUBLE PRECISION NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_A19A894CBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO epargne (id) SELECT id FROM __temp__epargne');
        $this->addSql('DROP TABLE __temp__epargne');
        $this->addSql('CREATE TEMPORARY TABLE __temp__membre AS SELECT id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr FROM membre');
        $this->addSql('DROP TABLE membre');
        $this->addSql('CREATE TABLE membre (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, telephone VARCHAR(30) NOT NULL, adresse VARCHAR(255) NOT NULL, login VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, groupement_id INTEGER NOT NULL, role_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_F6B4FB29E66695CE FOREIGN KEY (groupement_id) REFERENCES groupement (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F6B4FB29D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO membre (id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr) SELECT id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr FROM __temp__membre');
        $this->addSql('DROP TABLE __temp__membre');
        $this->addSql('CREATE INDEX IDX_F6B4FB29D60322AC ON membre (role_id)');
        $this->addSql('CREATE INDEX IDX_F6B4FB29E66695CE ON membre (groupement_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mode_cotisation AS SELECT id, is_libre, membre_id, campagne_id FROM mode_cotisation');
        $this->addSql('DROP TABLE mode_cotisation');
        $this->addSql('CREATE TABLE mode_cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, is_libre BOOLEAN NOT NULL, membre_id INTEGER NOT NULL, campagne_id INTEGER NOT NULL, CONSTRAINT FK_6BB9377E6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6BB9377E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO mode_cotisation (id, is_libre, membre_id, campagne_id) SELECT id, is_libre, membre_id, campagne_id FROM __temp__mode_cotisation');
        $this->addSql('DROP TABLE __temp__mode_cotisation');
        $this->addSql('CREATE INDEX IDX_6BB9377E16227374 ON mode_cotisation (campagne_id)');
        $this->addSql('CREATE INDEX IDX_6BB9377E6A99F74A ON mode_cotisation (membre_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_MODE_COTISATION_MEMBRE_CAMPAGNE ON mode_cotisation (membre_id, campagne_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__role AS SELECT id, nom FROM role');
        $this->addSql('DROP TABLE role');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, groupement_id INTEGER NOT NULL, CONSTRAINT FK_57698A6AE66695CE FOREIGN KEY (groupement_id) REFERENCES groupement (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO role (id, nom) SELECT id, nom FROM __temp__role');
        $this->addSql('DROP TABLE __temp__role');
        $this->addSql('CREATE INDEX IDX_57698A6AE66695CE ON role (groupement_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__seance AS SELECT id, date_seance, frequence, statut, campagne_id FROM seance');
        $this->addSql('DROP TABLE seance');
        $this->addSql('CREATE TABLE seance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_seance DATE NOT NULL, frequence VARCHAR(100) NOT NULL, statut VARCHAR(50) NOT NULL, campagne_id INTEGER NOT NULL, admin_id INTEGER NOT NULL, CONSTRAINT FK_DF7DFD0E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DF7DFD0E642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO seance (id, date_seance, frequence, statut, campagne_id) SELECT id, date_seance, frequence, statut, campagne_id FROM __temp__seance');
        $this->addSql('DROP TABLE __temp__seance');
        $this->addSql('CREATE INDEX IDX_DF7DFD0E16227374 ON seance (campagne_id)');
        $this->addSql('CREATE INDEX IDX_DF7DFD0E642B8210 ON seance (admin_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tontine AS SELECT id FROM tontine');
        $this->addSql('DROP TABLE tontine');
        $this->addSql('CREATE TABLE tontine (id INTEGER NOT NULL, numero VARCHAR(100) NOT NULL, client VARCHAR(255) NOT NULL, PRIMARY KEY (id), CONSTRAINT FK_3F164B7FBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO tontine (id) SELECT id FROM __temp__tontine');
        $this->addSql('DROP TABLE __temp__tontine');
        $this->addSql('CREATE TEMPORARY TABLE __temp__type_campagne AS SELECT id, nom, description FROM type_campagne');
        $this->addSql('DROP TABLE type_campagne');
        $this->addSql('CREATE TABLE type_campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description CLOB NOT NULL)');
        $this->addSql('INSERT INTO type_campagne (id, nom, description) SELECT id, nom, description FROM __temp__type_campagne');
        $this->addSql('DROP TABLE __temp__type_campagne');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'The migration adds required subtype and ownership data that cannot be reconstructed safely by rollback.',
        );

        $this->addSql('CREATE TABLE membre_campagne (membre_id INTEGER NOT NULL, campagne_id INTEGER NOT NULL, PRIMARY KEY (membre_id, campagne_id), CONSTRAINT FK_E6F9B5256A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E6F9B52516227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E6F9B52516227374 ON membre_campagne (campagne_id)');
        $this->addSql('CREATE INDEX IDX_E6F9B5256A99F74A ON membre_campagne (membre_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__admin AS SELECT id FROM admin');
        $this->addSql('DROP TABLE admin');
        $this->addSql('CREATE TABLE admin (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES membre (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO admin (id) SELECT id FROM __temp__admin');
        $this->addSql('DROP TABLE __temp__admin');
        $this->addSql('CREATE TEMPORARY TABLE __temp__campagne AS SELECT id, nom, date_debut, montant, created_at, updated_at, type_campagne_id, discr FROM campagne');
        $this->addSql('DROP TABLE campagne');
        $this->addSql('CREATE TABLE campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, montant DOUBLE PRECISION NOT NULL, date_created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, type_campagne_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_539B5D165D3BA4E5 FOREIGN KEY (type_campagne_id) REFERENCES type_campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO campagne (id, nom, date_debut, montant, date_created_at, updated_at, type_campagne_id, discr) SELECT id, nom, date_debut, montant, created_at, updated_at, type_campagne_id, discr FROM __temp__campagne');
        $this->addSql('DROP TABLE __temp__campagne');
        $this->addSql('CREATE INDEX IDX_539B5D165D3BA4E5 ON campagne (type_campagne_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cotisation AS SELECT id, montant, date_paiement, statut, mode_cotisation_id, seance_id FROM cotisation');
        $this->addSql('DROP TABLE cotisation');
        $this->addSql('CREATE TABLE cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, montant DOUBLE PRECISION NOT NULL, date_paiement DATETIME DEFAULT NULL, statut VARCHAR(50) NOT NULL, mode_cotisation_id INTEGER NOT NULL, seance_id INTEGER NOT NULL, CONSTRAINT FK_AE64D2ED1A942B1D FOREIGN KEY (mode_cotisation_id) REFERENCES mode_cotisation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AE64D2EDE3797A94 FOREIGN KEY (seance_id) REFERENCES seance (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cotisation (id, montant, date_paiement, statut, mode_cotisation_id, seance_id) SELECT id, montant, date_paiement, statut, mode_cotisation_id, seance_id FROM __temp__cotisation');
        $this->addSql('DROP TABLE __temp__cotisation');
        $this->addSql('CREATE INDEX IDX_AE64D2ED1A942B1D ON cotisation (mode_cotisation_id)');
        $this->addSql('CREATE INDEX IDX_AE64D2EDE3797A94 ON cotisation (seance_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__epargne AS SELECT id FROM epargne');
        $this->addSql('DROP TABLE epargne');
        $this->addSql('CREATE TABLE epargne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_A19A894CBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO epargne (id) SELECT id FROM __temp__epargne');
        $this->addSql('DROP TABLE __temp__epargne');
        $this->addSql('CREATE TEMPORARY TABLE __temp__membre AS SELECT id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr FROM membre');
        $this->addSql('DROP TABLE membre');
        $this->addSql('CREATE TABLE membre (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, telephone VARCHAR(30) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, login VARCHAR(100) NOT NULL, password VARCHAR(255) NOT NULL, groupement_id INTEGER NOT NULL, role_id INTEGER NOT NULL, discr VARCHAR(255) NOT NULL, CONSTRAINT FK_F6B4FB29E66695CE FOREIGN KEY (groupement_id) REFERENCES groupement (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F6B4FB29D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO membre (id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr) SELECT id, nom_complet, telephone, adresse, login, password, groupement_id, role_id, discr FROM __temp__membre');
        $this->addSql('DROP TABLE __temp__membre');
        $this->addSql('CREATE INDEX IDX_F6B4FB29E66695CE ON membre (groupement_id)');
        $this->addSql('CREATE INDEX IDX_F6B4FB29D60322AC ON membre (role_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mode_cotisation AS SELECT id, is_libre, membre_id, campagne_id FROM mode_cotisation');
        $this->addSql('DROP TABLE mode_cotisation');
        $this->addSql('CREATE TABLE mode_cotisation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, is_libre BOOLEAN NOT NULL, membre_id INTEGER NOT NULL, campagne_id INTEGER NOT NULL, CONSTRAINT FK_6BB9377E6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6BB9377E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO mode_cotisation (id, is_libre, membre_id, campagne_id) SELECT id, is_libre, membre_id, campagne_id FROM __temp__mode_cotisation');
        $this->addSql('DROP TABLE __temp__mode_cotisation');
        $this->addSql('CREATE INDEX IDX_6BB9377E6A99F74A ON mode_cotisation (membre_id)');
        $this->addSql('CREATE INDEX IDX_6BB9377E16227374 ON mode_cotisation (campagne_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__role AS SELECT id, nom FROM role');
        $this->addSql('DROP TABLE role');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL)');
        $this->addSql('INSERT INTO role (id, nom) SELECT id, nom FROM __temp__role');
        $this->addSql('DROP TABLE __temp__role');
        $this->addSql('CREATE TEMPORARY TABLE __temp__seance AS SELECT id, date_seance, frequence, statut, campagne_id FROM seance');
        $this->addSql('DROP TABLE seance');
        $this->addSql('CREATE TABLE seance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_seance DATE NOT NULL, frequence VARCHAR(100) NOT NULL, statut VARCHAR(50) NOT NULL, campagne_id INTEGER NOT NULL, CONSTRAINT FK_DF7DFD0E16227374 FOREIGN KEY (campagne_id) REFERENCES campagne (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO seance (id, date_seance, frequence, statut, campagne_id) SELECT id, date_seance, frequence, statut, campagne_id FROM __temp__seance');
        $this->addSql('DROP TABLE __temp__seance');
        $this->addSql('CREATE INDEX IDX_DF7DFD0E16227374 ON seance (campagne_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tontine AS SELECT id FROM tontine');
        $this->addSql('DROP TABLE tontine');
        $this->addSql('CREATE TABLE tontine (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, CONSTRAINT FK_3F164B7FBF396750 FOREIGN KEY (id) REFERENCES campagne (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO tontine (id) SELECT id FROM __temp__tontine');
        $this->addSql('DROP TABLE __temp__tontine');
        $this->addSql('CREATE TEMPORARY TABLE __temp__type_campagne AS SELECT id, nom, description FROM type_campagne');
        $this->addSql('DROP TABLE type_campagne');
        $this->addSql('CREATE TABLE type_campagne (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO type_campagne (id, nom, description) SELECT id, nom, description FROM __temp__type_campagne');
        $this->addSql('DROP TABLE __temp__type_campagne');
    }
}
