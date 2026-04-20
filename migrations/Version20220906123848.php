<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906123848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_school_educations ADD finality_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_school_educations ADD CONSTRAINT FK_67EC5BC0967CF506 FOREIGN KEY (finality_id) REFERENCES app_school_finalities (id)');
        $this->addSql('CREATE INDEX IDX_67EC5BC0967CF506 ON app_school_educations (finality_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_school_educations DROP FOREIGN KEY FK_67EC5BC0967CF506');
        $this->addSql('DROP INDEX IDX_67EC5BC0967CF506 ON app_school_educations');
        $this->addSql('ALTER TABLE app_school_educations DROP finality_id');
    }
}
