<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191227192424 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_9AEACC13989D9B62 ON level (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E47989D9B62 ON channel (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_865F80C0989D9B62 ON duration (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_34DCD176989D9B62 ON person (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_865F80C0989D9B62');
        $this->addSql('DROP INDEX UNIQ_34DCD176989D9B62');
        $this->addSql('DROP INDEX UNIQ_9AEACC13989D9B62');
        $this->addSql('DROP INDEX UNIQ_A2F98E47989D9B62');
    }
}
