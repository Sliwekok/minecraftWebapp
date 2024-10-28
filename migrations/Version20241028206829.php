<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018203321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE config ADD online_mode bit not null');
        $this->addSql('ALTER TABLE config ADD require_resource_pack bit not null');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mods DROP COLUMN summary');
    }
}
