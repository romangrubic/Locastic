<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220601150148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, race_name VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE results (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, full_name VARCHAR(255) NOT NULL, race_time TIME NOT NULL, distance VARCHAR(6) NOT NULL, placement INT NOT NULL, INDEX IDX_9FA3E4146E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4146E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4146E59D40D');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE results');
    }
}
