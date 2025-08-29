<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829154245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT fk_6a2ca10c399c4167
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6a2ca10c399c4167
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP site_highlight_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD site_highlight_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT fk_6a2ca10c399c4167 FOREIGN KEY (site_highlight_id) REFERENCES site_highlight (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_6a2ca10c399c4167 ON media (site_highlight_id)
        SQL);
    }
}
