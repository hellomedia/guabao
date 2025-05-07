<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507161925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER love_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER love_level DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER local_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER local_level DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER healthy_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER healthy_level DROP NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER love_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER love_level SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER local_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER local_level SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER healthy_level TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ALTER healthy_level SET NOT NULL
        SQL);
    }
}
