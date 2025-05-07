<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507161737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP CONSTRAINT fk_d43829f78ad350ab
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_d43829f78ad350ab
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food RENAME COLUMN food_type_id TO type_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD CONSTRAINT FK_D43829F7C54C8C93 FOREIGN KEY (type_id) REFERENCES food_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D43829F7C54C8C93 ON food (type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP CONSTRAINT FK_D43829F7C54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D43829F7C54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food RENAME COLUMN type_id TO food_type_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD CONSTRAINT fk_d43829f78ad350ab FOREIGN KEY (food_type_id) REFERENCES food_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d43829f78ad350ab ON food (food_type_id)
        SQL);
    }
}
