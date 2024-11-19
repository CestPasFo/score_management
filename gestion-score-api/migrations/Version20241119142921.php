<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119142921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, joueurs_id INT NOT NULL, nom VARCHAR(30) NOT NULL, nbdefaite INT DEFAULT NULL, nbvictoire INT DEFAULT NULL, nbmatch INT NOT NULL, INDEX IDX_2449BA15A3DC7281 (joueurs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, firstname VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE score (id INT AUTO_INCREMENT NOT NULL, equipe_a_id INT NOT NULL, equipe_b_id INT NOT NULL, score VARCHAR(70) NOT NULL, UNIQUE INDEX UNIQ_329937513297C2A6 (equipe_a_id), UNIQUE INDEX UNIQ_3299375120226D48 (equipe_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueur (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_329937513297C2A6 FOREIGN KEY (equipe_a_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375120226D48 FOREIGN KEY (equipe_b_id) REFERENCES equipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15A3DC7281');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_329937513297C2A6');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375120226D48');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE score');
    }
}
