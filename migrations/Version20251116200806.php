<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116200806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE country DROP CONSTRAINT fk_5373c966ba6a01ab
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_5373c966ba6a01ab
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE country DROP food_cover_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD cover_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD CONSTRAINT FK_D43829F7922726E9 FOREIGN KEY (cover_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D43829F7922726E9 ON food (cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT fk_7656f53bba6a01ab
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_7656f53bba6a01ab
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP food_cover_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD food_cover_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT fk_7656f53bba6a01ab FOREIGN KEY (food_cover_id) REFERENCES media (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_7656f53bba6a01ab ON trip (food_cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE country ADD food_cover_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE country ADD CONSTRAINT fk_5373c966ba6a01ab FOREIGN KEY (food_cover_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_5373c966ba6a01ab ON country (food_cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP CONSTRAINT FK_D43829F7922726E9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D43829F7922726E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP cover_id
        SQL);
    }
}
