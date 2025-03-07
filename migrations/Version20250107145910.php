<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250107145910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config ADD online_mode BIT NOT NULL default 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config DROP COLUMN online_mode');
    }
}
