<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906091344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_school_finalities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_school_finality_school_level (school_finality_id INT NOT NULL, school_level_id INT NOT NULL, INDEX IDX_91E9EA11C57AC7B6 (school_finality_id), INDEX IDX_91E9EA11A1F77FE3 (school_level_id), PRIMARY KEY(school_finality_id, school_level_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_school_finality_school_level ADD CONSTRAINT FK_91E9EA11C57AC7B6 FOREIGN KEY (school_finality_id) REFERENCES app_school_finalities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE app_school_finality_school_level ADD CONSTRAINT FK_91E9EA11A1F77FE3 FOREIGN KEY (school_level_id) REFERENCES app_school_levels (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_school_finality_school_level DROP FOREIGN KEY FK_91E9EA11C57AC7B6');
        $this->addSql('DROP TABLE app_school_finalities');
        $this->addSql('DROP TABLE app_school_finality_school_level');
    }
}
