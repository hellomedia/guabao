<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205133055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_food (site_highlight_id INT NOT NULL, food_id INT NOT NULL, PRIMARY KEY(site_highlight_id, food_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_96328A9B399C4167 ON site_highlight_food (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_96328A9BBA8E87C4 ON site_highlight_food (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_food ADD CONSTRAINT FK_96328A9B399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_food ADD CONSTRAINT FK_96328A9BBA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_food DROP CONSTRAINT FK_96328A9B399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_food DROP CONSTRAINT FK_96328A9BBA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_food
        SQL);
    }
}
