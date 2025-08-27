<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827083347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE story_media_tag (story_id INT NOT NULL, media_tag_id INT NOT NULL, PRIMARY KEY(story_id, media_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B162FF0FAA5D4036 ON story_media_tag (story_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B162FF0F6ABF9CF ON story_media_tag (media_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_media_tag ADD CONSTRAINT FK_B162FF0FAA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_media_tag ADD CONSTRAINT FK_B162FF0F6ABF9CF FOREIGN KEY (media_tag_id) REFERENCES media_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE story_media_tag DROP CONSTRAINT FK_B162FF0FAA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_media_tag DROP CONSTRAINT FK_B162FF0F6ABF9CF
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE story_media_tag
        SQL);
    }
}
