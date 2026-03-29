<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260329090757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit ADD visitor_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit ADD CONSTRAINT FK_E0A8ABBF70BEE6D FOREIGN KEY (visitor_id) REFERENCES anonymous_visitor (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0A8ABBF70BEE6D ON anonymous_visit (visitor_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit DROP CONSTRAINT FK_E0A8ABBF70BEE6D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E0A8ABBF70BEE6D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE anonymous_visit DROP visitor_id
        SQL);
    }
}
