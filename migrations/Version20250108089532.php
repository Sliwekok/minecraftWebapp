<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108089532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
ALTER TABLE config ADD pause_when_empty INT NOT NULL DEFAULT -1;
ALTER TABLE config ADD spawn_monsters  BIT NOT NULL DEFAULT 1;
');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
ALTER TABLE config DROP COLUMN pause_when_empty;
ALTER TABLE config DROP COLUMN spawn_monsters;
');
    }
}
