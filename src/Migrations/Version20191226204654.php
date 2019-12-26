<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191226204654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE level_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE duration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE person_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE level (id INT NOT NULL, name VARCHAR(50) NOT NULL, display_order SMALLINT NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE duration (id INT NOT NULL, label VARCHAR(50) NOT NULL, display_order INT NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE person (id INT NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE person_video (person_id INT NOT NULL, video_id INT NOT NULL, PRIMARY KEY(person_id, video_id))');
        $this->addSql('CREATE INDEX IDX_D0F1C5E8217BBB47 ON person_video (person_id)');
        $this->addSql('CREATE INDEX IDX_D0F1C5E829C1004E ON person_video (video_id)');
        $this->addSql('ALTER TABLE person_video ADD CONSTRAINT FK_D0F1C5E8217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE person_video ADD CONSTRAINT FK_D0F1C5E829C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video ADD level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD duration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C5FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C37B987D8 FOREIGN KEY (duration_id) REFERENCES duration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C5FB14BA7 ON video (level_id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C37B987D8 ON video (duration_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C5FB14BA7');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C37B987D8');
        $this->addSql('ALTER TABLE person_video DROP CONSTRAINT FK_D0F1C5E8217BBB47');
        $this->addSql('DROP SEQUENCE level_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE duration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE person_id_seq CASCADE');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE duration');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE person_video');
        $this->addSql('DROP INDEX IDX_7CC7DA2C5FB14BA7');
        $this->addSql('DROP INDEX IDX_7CC7DA2C37B987D8');
        $this->addSql('ALTER TABLE video DROP level_id');
        $this->addSql('ALTER TABLE video DROP duration_id');
    }
}
