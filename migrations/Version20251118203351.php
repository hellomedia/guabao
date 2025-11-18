<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118203351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE ingredient_similar (ingredient_source INT NOT NULL, ingredient_target INT NOT NULL, PRIMARY KEY(ingredient_source, ingredient_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CA8C335BB3F0BF32 ON ingredient_similar (ingredient_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CA8C335BAA15EFBD ON ingredient_similar (ingredient_target)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient_similar ADD CONSTRAINT FK_CA8C335BB3F0BF32 FOREIGN KEY (ingredient_source) REFERENCES ingredient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient_similar ADD CONSTRAINT FK_CA8C335BAA15EFBD FOREIGN KEY (ingredient_target) REFERENCES ingredient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient_similar DROP CONSTRAINT FK_CA8C335BB3F0BF32
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ingredient_similar DROP CONSTRAINT FK_CA8C335BAA15EFBD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ingredient_similar
        SQL);
    }
}
