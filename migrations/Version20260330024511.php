<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330024511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visit_country_code ON anonymous_visit (country_code)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visit_city_name ON anonymous_visit (city_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visitor_visitor_id ON anonymous_visitor (visitor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visitor_page_count ON anonymous_visitor (page_count)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visitor_alias ON anonymous_visitor (alias)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visit_country_code
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visit_city_name
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visitor_visitor_id
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visitor_page_count
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visitor_alias
        SQL);
    }
}
