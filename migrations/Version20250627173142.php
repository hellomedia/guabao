<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627173142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER filename DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER original_filename DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER token DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER path DROP NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER filename SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER original_filename SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER token SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ALTER path SET NOT NULL
        SQL);
    }
}
