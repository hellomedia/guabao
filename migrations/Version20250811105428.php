<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250811105428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE INDEX media_taken_at_idx ON media (taken_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX trip_started_at_idx ON trip (started_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX trip_duration_idx ON trip (duration)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX media_taken_at_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX trip_started_at_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX trip_duration_idx
        SQL);
    }
}
