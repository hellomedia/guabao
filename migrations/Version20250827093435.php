<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827093435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE food_cuisine (food_id INT NOT NULL, cuisine_id INT NOT NULL, PRIMARY KEY(food_id, cuisine_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F4AF053BA8E87C4 ON food_cuisine (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5F4AF053ED4BAC14 ON food_cuisine (cuisine_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_cuisine ADD CONSTRAINT FK_5F4AF053BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_cuisine ADD CONSTRAINT FK_5F4AF053ED4BAC14 FOREIGN KEY (cuisine_id) REFERENCES cuisine (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP season_start
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP season_end
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ADD season_start INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient ADD season_end INT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food_cuisine DROP CONSTRAINT FK_5F4AF053BA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_cuisine DROP CONSTRAINT FK_5F4AF053ED4BAC14
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_cuisine
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD season_start INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD season_end INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient DROP season_start
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient DROP season_end
        SQL);
    }
}
