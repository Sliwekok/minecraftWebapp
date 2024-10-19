<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018203026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mods ADD website_url NVARCHAR(255)');
        $this->addSql('ALTER TABLE mods ADD thumbnail NVARCHAR(255)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mods DROP COLUMN website_url');
        $this->addSql('ALTER TABLE mods DROP COLUMN thumbnail');
    }
}
