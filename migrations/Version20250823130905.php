<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823130905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            UPDATE media SET show_in_trip = TRUE WHERE in_default_gallery = TRUE
        SQL);
        $this->addSql(<<<'SQL'
            UPDATE media SET show_in_story = TRUE WHERE story_id IS NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            UPDATE media SET show_in_food = TRUE WHERE food_id IS NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {

    }
}
