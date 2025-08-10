<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250810204148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ALTER short_name_fr TYPE VARCHAR(35)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ALTER short_name_en TYPE VARCHAR(35)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ALTER short_name_fr TYPE VARCHAR(25)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ALTER short_name_en TYPE VARCHAR(25)
        SQL);
    }
}
