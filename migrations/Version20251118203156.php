<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118203156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP CONSTRAINT fk_d43829f7ed4bac14
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_d43829f7ed4bac14
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP cuisine_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD cuisine_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD CONSTRAINT fk_d43829f7ed4bac14 FOREIGN KEY (cuisine_id) REFERENCES cuisine (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d43829f7ed4bac14 ON food (cuisine_id)
        SQL);
    }
}
