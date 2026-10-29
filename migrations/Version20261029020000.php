<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261029020000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align the schema with the final Groupement and Exercice model without dropping existing data.';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            !$schema->hasTable('groupement'),
            'Migration stopped: the groupement table is missing; the schema is not ready for this migration.'
        );

        if (!$schema->getTable('groupement')->hasColumn('telephone')) {
            $this->addSql('ALTER TABLE groupement ADD COLUMN telephone VARCHAR(30) DEFAULT NULL');
        }
        if (!$schema->getTable('groupement')->hasColumn('adresse')) {
            $this->addSql('ALTER TABLE groupement ADD COLUMN adresse VARCHAR(255) DEFAULT NULL');
        }
        if (!$schema->getTable('groupement')->hasColumn('statut')) {
            $this->addSql("ALTER TABLE groupement ADD COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'actif'");
        }
        if (!$schema->getTable('groupement')->hasColumn('created_at')) {
            $this->addSql('ALTER TABLE groupement ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        }
        if (!$schema->getTable('groupement')->hasColumn('updated_at')) {
            $this->addSql('ALTER TABLE groupement ADD COLUMN updated_at DATETIME DEFAULT NULL');
        }

        if (!$schema->hasTable('exercice')) {
            $this->addSql('CREATE TABLE exercice (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, date_limite_pret DATE DEFAULT NULL, date_retribution DATE DEFAULT NULL, statut VARCHAR(20) NOT NULL DEFAULT ' . "'en_cours'" . ', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, groupement_id INTEGER NOT NULL, CONSTRAINT FK_EXERCICE_GROUPEMENT FOREIGN KEY (groupement_id) REFERENCES groupement (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE INDEX IDX_AE2B55A0E66695CE ON exercice (groupement_id)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'This migration preserves data; rollback would otherwise drop newly added fields and the exercice table.'
        );
    }
}
