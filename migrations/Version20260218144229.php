<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260218144229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD name_search VARCHAR(150) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX food_name_search_idx ON food (name_search)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ADD name_search VARCHAR(150) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX ingredient_name_search_idx ON ingredient (name_search)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX food_name_search_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP name_search
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX ingredient_name_search_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient DROP name_search
        SQL);
    }
}
