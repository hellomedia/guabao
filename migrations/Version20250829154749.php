<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829154749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_media (site_highlight_id INT NOT NULL, media_id INT NOT NULL, PRIMARY KEY(site_highlight_id, media_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2E6A86DC399C4167 ON site_highlight_media (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2E6A86DCEA9FDD75 ON site_highlight_media (media_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_story (site_highlight_id INT NOT NULL, story_id INT NOT NULL, PRIMARY KEY(site_highlight_id, story_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF1023E8399C4167 ON site_highlight_story (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AF1023E8AA5D4036 ON site_highlight_story (story_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_trip (site_highlight_id INT NOT NULL, trip_id INT NOT NULL, PRIMARY KEY(site_highlight_id, trip_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_345C5657399C4167 ON site_highlight_trip (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_345C5657A5BC2E0E ON site_highlight_trip (trip_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_media ADD CONSTRAINT FK_2E6A86DC399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_media ADD CONSTRAINT FK_2E6A86DCEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_story ADD CONSTRAINT FK_AF1023E8399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_story ADD CONSTRAINT FK_AF1023E8AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_trip ADD CONSTRAINT FK_345C5657399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_trip ADD CONSTRAINT FK_345C5657A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_media DROP CONSTRAINT FK_2E6A86DC399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_media DROP CONSTRAINT FK_2E6A86DCEA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_story DROP CONSTRAINT FK_AF1023E8399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_story DROP CONSTRAINT FK_AF1023E8AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_trip DROP CONSTRAINT FK_345C5657399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_trip DROP CONSTRAINT FK_345C5657A5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_story
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_trip
        SQL);
    }
}
