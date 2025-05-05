<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505165542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE picture DROP CONSTRAINT FK_16DB4F89A5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture DROP CONSTRAINT FK_16DB4F89A3AD38F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89A3AD38F FOREIGN KEY (highlighted_trip_id) REFERENCES trip (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE picture DROP CONSTRAINT fk_16db4f89a5bc2e0e
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture DROP CONSTRAINT fk_16db4f89a3ad38f
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture ADD CONSTRAINT fk_16db4f89a5bc2e0e FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE picture ADD CONSTRAINT fk_16db4f89a3ad38f FOREIGN KEY (highlighted_trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
