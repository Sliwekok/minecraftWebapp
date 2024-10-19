<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241004222042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mods (id INT IDENTITY NOT NULL, server_id INT, name NVARCHAR(255) NOT NULL, url NVARCHAR(255), size DOUBLE PRECISION NOT NULL, added_at DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_631EF2FA1844E6B7 ON mods (server_id)');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'mods\', N\'COLUMN\', added_at');
        $this->addSql('ALTER TABLE mods ADD CONSTRAINT FK_631EF2FA1844E6B7 FOREIGN KEY (server_id) REFERENCES server (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mods DROP CONSTRAINT FK_631EF2FA1844E6B7');
        $this->addSql('DROP TABLE mods');
    }
}
