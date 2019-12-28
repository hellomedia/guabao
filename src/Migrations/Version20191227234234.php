<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191227234234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE video_tag_id_seq CASCADE');
        $this->addSql('ALTER TABLE video_tag DROP CONSTRAINT FK_F910728729C1004E');
        $this->addSql('ALTER TABLE video_tag DROP CONSTRAINT FK_F9107287BAD26311');
        $this->addSql('ALTER TABLE video_tag DROP id');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT FK_F910728729C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT FK_F9107287BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_tag ADD PRIMARY KEY (video_id, tag_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE video_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE video_tag DROP CONSTRAINT fk_f910728729c1004e');
        $this->addSql('ALTER TABLE video_tag DROP CONSTRAINT fk_f9107287bad26311');
        $this->addSql('DROP INDEX video_tag_pkey');
        $this->addSql('ALTER TABLE video_tag ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT fk_f910728729c1004e FOREIGN KEY (video_id) REFERENCES video (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_tag ADD CONSTRAINT fk_f9107287bad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_tag ADD PRIMARY KEY (id)');
    }
}
