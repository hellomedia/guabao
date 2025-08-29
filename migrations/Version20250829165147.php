<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829165147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_place (site_highlight_id INT NOT NULL, place_id INT NOT NULL, PRIMARY KEY(site_highlight_id, place_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_305B741D399C4167 ON site_highlight_place (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_305B741DDA6A219 ON site_highlight_place (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE site_highlight_meal (site_highlight_id INT NOT NULL, meal_id INT NOT NULL, PRIMARY KEY(site_highlight_id, meal_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DCFC2DF0399C4167 ON site_highlight_meal (site_highlight_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DCFC2DF0639666D6 ON site_highlight_meal (meal_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_place ADD CONSTRAINT FK_305B741D399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_place ADD CONSTRAINT FK_305B741DDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_meal ADD CONSTRAINT FK_DCFC2DF0399C4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_meal ADD CONSTRAINT FK_DCFC2DF0639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_place DROP CONSTRAINT FK_305B741D399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_place DROP CONSTRAINT FK_305B741DDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_meal DROP CONSTRAINT FK_DCFC2DF0399C4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE site_highlight_meal DROP CONSTRAINT FK_DCFC2DF0639666D6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_place
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE site_highlight_meal
        SQL);
    }
}
