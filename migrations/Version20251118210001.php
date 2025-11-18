<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118210001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE food_similar (food_source INT NOT NULL, food_target INT NOT NULL, PRIMARY KEY(food_source, food_target))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8424E4A51FA415D0 ON food_similar (food_source)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8424E4A5641455F ON food_similar (food_target)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_similar ADD CONSTRAINT FK_8424E4A51FA415D0 FOREIGN KEY (food_source) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_similar ADD CONSTRAINT FK_8424E4A5641455F FOREIGN KEY (food_target) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE food_similar DROP CONSTRAINT FK_8424E4A51FA415D0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_similar DROP CONSTRAINT FK_8424E4A5641455F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_similar
        SQL);
    }
}
