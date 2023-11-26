<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231126231245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config (id INT IDENTITY NOT NULL, server_id INT, port INT NOT NULL, max_ram INT NOT NULL, pvp BIT NOT NULL, hardcore BIT NOT NULL, max_players INT NOT NULL, whitelist BIT NOT NULL, difficulty NVARCHAR(255) NOT NULL, allow_flight BIT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D48A2F7C1844E6B7 ON config (server_id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C1844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
    }

    public function down(Schema $schema): void
    {
    }
}
