<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823130904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD show_in_trip BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD show_in_story BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD show_in_food BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX media_show_in_trip_idx ON media (show_in_trip)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX media_show_in_story_idx ON media (show_in_story)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX media_show_in_food_idx ON media (show_in_food)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX media_show_in_trip_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX media_show_in_story_idx
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX media_show_in_food_idx
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP show_in_trip
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP show_in_story
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP show_in_food
        SQL);
    }
}
