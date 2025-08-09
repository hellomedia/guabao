<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250809162238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD parent_trip_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53BD7F45A48 FOREIGN KEY (parent_trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7656F53BD7F45A48 ON trip (parent_trip_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53BD7F45A48
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7656F53BD7F45A48
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP parent_trip_id
        SQL);
    }
}
