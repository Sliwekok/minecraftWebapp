<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130231448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE config ADD motd NVARCHAR(255) NOT NULL DEFAULT 'motd'");
        $this->addSql("ALTER TABLE config ADD level_name NVARCHAR(255) NOT NULL DEFAULT 'level_name'");
    }

    public function down(Schema $schema): void
    {
    }
}
