<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827104056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE media_food (media_id INT NOT NULL, food_id INT NOT NULL, PRIMARY KEY(media_id, food_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_177FF624EA9FDD75 ON media_food (media_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_177FF624BA8E87C4 ON media_food (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_food ADD CONSTRAINT FK_177FF624EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_food ADD CONSTRAINT FK_177FF624BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT fk_6a2ca10cba8e87c4
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6a2ca10cba8e87c4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP food_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media_food DROP CONSTRAINT FK_177FF624EA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_food DROP CONSTRAINT FK_177FF624BA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media_food
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD food_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT fk_6a2ca10cba8e87c4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_6a2ca10cba8e87c4 ON media (food_id)
        SQL);
    }
}
