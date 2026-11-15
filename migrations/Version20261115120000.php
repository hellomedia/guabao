<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261115120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Food: rename duplicate slugs (fried-rice, chicken-rice) and make slug_en / slug_fr unique';
    }

    public function up(Schema $schema): void
    {
        // Same rule as SluggableListener: the oldest food keeps the plain slug, the others get
        // their first cuisine's slug appended, else a number (fried-rice, fried-rice-thai, fried-rice-2).
        foreach (['slug_en', 'slug_fr'] as $column) {
            $this->addSql(<<<SQL
                UPDATE food f SET {$column} = f.{$column} || '-' || c.{$column}
                FROM food_cuisine fc JOIN cuisine c ON c.id = fc.cuisine_id
                WHERE fc.food_id = f.id
                  AND fc.cuisine_id = (SELECT MIN(cuisine_id) FROM food_cuisine WHERE food_id = f.id)
                  AND EXISTS (SELECT 1 FROM food o WHERE o.{$column} = f.{$column} AND o.id < f.id)
                  AND NOT EXISTS (SELECT 1 FROM food o WHERE o.{$column} = f.{$column} || '-' || c.{$column})
                SQL);
            $this->addSql(<<<SQL
                UPDATE food f SET {$column} = f.{$column} || '-' || d.n
                FROM (SELECT id, ROW_NUMBER() OVER (PARTITION BY {$column} ORDER BY id) AS n FROM food) d
                WHERE d.id = f.id AND d.n > 1
                SQL);
        }

        $this->addSql('CREATE UNIQUE INDEX food_slug_en_unique ON food (slug_en)');
        $this->addSql('CREATE UNIQUE INDEX food_slug_fr_unique ON food (slug_fr)');
    }

    public function down(Schema $schema): void
    {
        // The renamed slugs are kept
        $this->addSql('DROP INDEX food_slug_en_unique');
        $this->addSql('DROP INDEX food_slug_fr_unique');
    }
}
