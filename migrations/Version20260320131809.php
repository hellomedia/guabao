<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320131809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE place ADD chain_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place ADD CONSTRAINT FK_741D53CD966C2F62 FOREIGN KEY (chain_id) REFERENCES chain (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_741D53CD966C2F62 ON place (chain_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE place DROP CONSTRAINT FK_741D53CD966C2F62
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_741D53CD966C2F62
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place DROP chain_id
        SQL);
    }
}
