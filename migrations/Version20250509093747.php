<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509093747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53B922726E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53BBA6A01AB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53B922726E9 FOREIGN KEY (cover_id) REFERENCES picture (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53BBA6A01AB FOREIGN KEY (food_cover_id) REFERENCES picture (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT fk_7656f53b922726e9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT fk_7656f53bba6a01ab
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT fk_7656f53b922726e9 FOREIGN KEY (cover_id) REFERENCES picture (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT fk_7656f53bba6a01ab FOREIGN KEY (food_cover_id) REFERENCES picture (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
