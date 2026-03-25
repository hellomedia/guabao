<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260325015135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit ADD alias VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit ADD ip VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visit_visitor_id ON anonymous_visit (visitor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_anonymous_visit_started_at ON anonymous_visit (started_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visit_visitor_id
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_anonymous_visit_started_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit DROP alias
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit DROP ip
        SQL);
    }
}
