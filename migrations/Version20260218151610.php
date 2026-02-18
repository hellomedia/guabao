<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260218151610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER name_search TYPE VARCHAR(300)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ALTER name_search TYPE VARCHAR(300)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ALTER name_search TYPE VARCHAR(150)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER name_search TYPE VARCHAR(150)
        SQL);
    }
}
