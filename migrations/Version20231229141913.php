<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229141913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE [backup] (id INT IDENTITY NOT NULL, server_id INT, created_at DATETIME2(6) NOT NULL, size INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_3FF0D1AC1844E6B7 ON [backup] (server_id)');
        $this->addSql('ALTER TABLE [backup] ADD CONSTRAINT FK_3FF0D1AC1844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
