<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509203221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE meal_place_tag (meal_id INT NOT NULL, place_tag_id INT NOT NULL, PRIMARY KEY(meal_id, place_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B254D379639666D6 ON meal_place_tag (meal_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B254D379181C6478 ON meal_place_tag (place_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag ADD CONSTRAINT FK_B254D379639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag ADD CONSTRAINT FK_B254D379181C6478 FOREIGN KEY (place_tag_id) REFERENCES place_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag DROP CONSTRAINT FK_B254D379639666D6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag DROP CONSTRAINT FK_B254D379181C6478
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE meal_place_tag
        SQL);
    }
}
