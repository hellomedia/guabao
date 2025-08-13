<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250813082333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT fk_7656f53bd7f45a48
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_7656f53bd7f45a48
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip RENAME COLUMN parent_trip_id TO parent_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53B727ACA70 FOREIGN KEY (parent_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7656F53B727ACA70 ON trip (parent_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53B727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7656F53B727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip RENAME COLUMN parent_id TO parent_trip_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT fk_7656f53bd7f45a48 FOREIGN KEY (parent_trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_7656f53bd7f45a48 ON trip (parent_trip_id)
        SQL);
    }
}
