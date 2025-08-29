<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829073827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE story_place_tag (story_id INT NOT NULL, place_tag_id INT NOT NULL, PRIMARY KEY(story_id, place_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F581EB03AA5D4036 ON story_place_tag (story_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F581EB03181C6478 ON story_place_tag (place_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_place_tag ADD CONSTRAINT FK_F581EB03AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_place_tag ADD CONSTRAINT FK_F581EB03181C6478 FOREIGN KEY (place_tag_id) REFERENCES place_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE story_place_tag DROP CONSTRAINT FK_F581EB03AA5D4036
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE story_place_tag DROP CONSTRAINT FK_F581EB03181C6478
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE story_place_tag
        SQL);
    }
}
