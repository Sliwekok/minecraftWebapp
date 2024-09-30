<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231121191056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
if object_id('dbo.login', 'U') is null begin
    CREATE TABLE dbo.login(
        id int IDENTITY(1,1) PRIMARY KEY,
        username nvarchar(50) NOT NULL,
        email nvarchar(50) NOT NULL,
        password nvarchar(255) NOT NULL,
        roles nvarchar(255) NOT NULL,
        is_active bit NOT NULL DEFAULT  1,
        modification_date datetime NOT NULL DEFAULT GETDATE()
        )  ON [PRIMARY]
end
SQL
        );
    }

    public function down(Schema $schema): void
    {

    }
}
