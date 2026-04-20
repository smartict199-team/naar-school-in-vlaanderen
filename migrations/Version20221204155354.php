<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221204155354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_school_education_underrepresented_groups (id INT AUTO_INCREMENT NOT NULL, school_education_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, position INT NOT NULL, capacity INT DEFAULT NULL, student_seats_percentage DOUBLE PRECISION DEFAULT NULL, student_seats_percentage_visible TINYINT(1) NOT NULL, student_seats_taken INT DEFAULT NULL, INDEX IDX_B1D86470E79308CF (school_education_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE app_school_education_underrepresented_groups ADD CONSTRAINT FK_B1D86470E79308CF FOREIGN KEY (school_education_id) REFERENCES app_school_educations (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE app_school_education_underrepresented_groups');
    }
}
