<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250515075916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE country (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, code VARCHAR(2) DEFAULT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, food_cover_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_5373C966BA6A01AB ON country (food_cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cuisine (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE food (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, love_level INT DEFAULT NULL, local_level INT DEFAULT NULL, healthy_level INT DEFAULT NULL, season_start INT DEFAULT NULL, season_end INT DEFAULT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, cuisine_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D43829F7ED4BAC14 ON food (cuisine_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE food_food_tag (food_id INT NOT NULL, food_tag_id INT NOT NULL, PRIMARY KEY(food_id, food_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DCA3707CBA8E87C4 ON food_food_tag (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DCA3707CFAFC09B2 ON food_food_tag (food_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE food_ingredient (food_id INT NOT NULL, ingredient_id INT NOT NULL, PRIMARY KEY(food_id, ingredient_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CEAC8D1BA8E87C4 ON food_ingredient (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CEAC8D1933FE08C ON food_ingredient (ingredient_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE food_tag (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ingredient (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, favourite BOOLEAN DEFAULT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE meal (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, enjoyed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, place_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9EF68E9CDA6A219 ON meal (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE meal_place_tag (meal_id INT NOT NULL, place_tag_id INT NOT NULL, PRIMARY KEY(meal_id, place_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B254D379639666D6 ON meal_place_tag (meal_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B254D379181C6478 ON meal_place_tag (place_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE media (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, taken_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, highlight BOOLEAN DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, is_meal BOOLEAN DEFAULT NULL, is_pano BOOLEAN DEFAULT NULL, is360 BOOLEAN DEFAULT NULL, type VARCHAR(255) NOT NULL, video_url VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, food_id INT DEFAULT NULL, trip_id INT DEFAULT NULL, highlighted_trip_id INT DEFAULT NULL, place_id INT DEFAULT NULL, meal_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A2CA10CBA8E87C4 ON media (food_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A2CA10CA5BC2E0E ON media (trip_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A2CA10CA3AD38F ON media (highlighted_trip_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A2CA10CDA6A219 ON media (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6A2CA10C639666D6 ON media (meal_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE media_place_tag (media_id INT NOT NULL, place_tag_id INT NOT NULL, PRIMARY KEY(media_id, place_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_295B62FCEA9FDD75 ON media_place_tag (media_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_295B62FC181C6478 ON media_place_tag (place_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE media_media_tag (media_id INT NOT NULL, media_tag_id INT NOT NULL, PRIMARY KEY(media_id, media_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6DB876F0EA9FDD75 ON media_media_tag (media_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6DB876F06ABF9CF ON media_media_tag (media_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE media_tag (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, google_place_id VARCHAR(64) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place_place_tag (place_id INT NOT NULL, place_tag_id INT NOT NULL, PRIMARY KEY(place_id, place_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_78A0580FDA6A219 ON place_place_tag (place_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_78A0580F181C6478 ON place_place_tag (place_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE place_tag (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, country_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C3BD172F92F3E70 ON place_tag (country_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, headline_fr VARCHAR(255) DEFAULT NULL, headline_en VARCHAR(255) DEFAULT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, key VARCHAR(100) NOT NULL, started_at DATE NOT NULL, ended_at DATE NOT NULL, cover_id INT DEFAULT NULL, food_cover_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7656F53B8A90ABA9 ON trip (key)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7656F53B922726E9 ON trip (cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7656F53BBA6A01AB ON trip (food_cover_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip_country (trip_id INT NOT NULL, country_id INT NOT NULL, PRIMARY KEY(trip_id, country_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659F8CCBA5BC2E0E ON trip_country (trip_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_659F8CCBF92F3E70 ON trip_country (country_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip_trip_tag (trip_id INT NOT NULL, trip_tag_id INT NOT NULL, PRIMARY KEY(trip_id, trip_tag_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_27117D29A5BC2E0E ON trip_trip_tag (trip_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_27117D2955DD38BB ON trip_trip_tag (trip_tag_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip_tag (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, key VARCHAR(100) NOT NULL, name_fr VARCHAR(100) NOT NULL, name_en VARCHAR(100) NOT NULL, slug_fr VARCHAR(100) NOT NULL, slug_en VARCHAR(100) NOT NULL, description_fr TEXT DEFAULT NULL, description_en TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8F404E398A90ABA9 ON trip_tag (key)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_ (id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL, account_language VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, last_login TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, last_active_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, verified BOOLEAN NOT NULL, enabled BOOLEAN NOT NULL, closed BOOLEAN NOT NULL, locked BOOLEAN NOT NULL, hellbanned BOOLEAN NOT NULL, scam BOOLEAN DEFAULT NULL, demarchage BOOLEAN DEFAULT NULL, watchlist BOOLEAN NOT NULL, whitelist BOOLEAN NOT NULL, admin_reviewed BOOLEAN NOT NULL, kotcop_score DOUBLE PRECISION DEFAULT NULL, kotcop_updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user_ (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE country ADD CONSTRAINT FK_5373C966BA6A01AB FOREIGN KEY (food_cover_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food ADD CONSTRAINT FK_D43829F7ED4BAC14 FOREIGN KEY (cuisine_id) REFERENCES cuisine (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_food_tag ADD CONSTRAINT FK_DCA3707CBA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_food_tag ADD CONSTRAINT FK_DCA3707CFAFC09B2 FOREIGN KEY (food_tag_id) REFERENCES food_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_ingredient ADD CONSTRAINT FK_CEAC8D1BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_ingredient ADD CONSTRAINT FK_CEAC8D1933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal ADD CONSTRAINT FK_9EF68E9CDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag ADD CONSTRAINT FK_B254D379639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag ADD CONSTRAINT FK_B254D379181C6478 FOREIGN KEY (place_tag_id) REFERENCES place_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CBA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA3AD38F FOREIGN KEY (highlighted_trip_id) REFERENCES trip (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_place_tag ADD CONSTRAINT FK_295B62FCEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_place_tag ADD CONSTRAINT FK_295B62FC181C6478 FOREIGN KEY (place_tag_id) REFERENCES place_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_media_tag ADD CONSTRAINT FK_6DB876F0EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_media_tag ADD CONSTRAINT FK_6DB876F06ABF9CF FOREIGN KEY (media_tag_id) REFERENCES media_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_place_tag ADD CONSTRAINT FK_78A0580FDA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_place_tag ADD CONSTRAINT FK_78A0580F181C6478 FOREIGN KEY (place_tag_id) REFERENCES place_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_tag ADD CONSTRAINT FK_C3BD172F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user_ (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53B922726E9 FOREIGN KEY (cover_id) REFERENCES media (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53BBA6A01AB FOREIGN KEY (food_cover_id) REFERENCES media (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_country ADD CONSTRAINT FK_659F8CCBA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_country ADD CONSTRAINT FK_659F8CCBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_trip_tag ADD CONSTRAINT FK_27117D29A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_trip_tag ADD CONSTRAINT FK_27117D2955DD38BB FOREIGN KEY (trip_tag_id) REFERENCES trip_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE country DROP CONSTRAINT FK_5373C966BA6A01AB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food DROP CONSTRAINT FK_D43829F7ED4BAC14
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_food_tag DROP CONSTRAINT FK_DCA3707CBA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_food_tag DROP CONSTRAINT FK_DCA3707CFAFC09B2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_ingredient DROP CONSTRAINT FK_CEAC8D1BA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE food_ingredient DROP CONSTRAINT FK_CEAC8D1933FE08C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal DROP CONSTRAINT FK_9EF68E9CDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag DROP CONSTRAINT FK_B254D379639666D6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE meal_place_tag DROP CONSTRAINT FK_B254D379181C6478
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CBA8E87C4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CA3AD38F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C639666D6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_place_tag DROP CONSTRAINT FK_295B62FCEA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_place_tag DROP CONSTRAINT FK_295B62FC181C6478
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_media_tag DROP CONSTRAINT FK_6DB876F0EA9FDD75
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE media_media_tag DROP CONSTRAINT FK_6DB876F06ABF9CF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_place_tag DROP CONSTRAINT FK_78A0580FDA6A219
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_place_tag DROP CONSTRAINT FK_78A0580F181C6478
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE place_tag DROP CONSTRAINT FK_C3BD172F92F3E70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53B922726E9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP CONSTRAINT FK_7656F53BBA6A01AB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_country DROP CONSTRAINT FK_659F8CCBA5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_country DROP CONSTRAINT FK_659F8CCBF92F3E70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_trip_tag DROP CONSTRAINT FK_27117D29A5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_trip_tag DROP CONSTRAINT FK_27117D2955DD38BB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE country
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cuisine
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_food_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_ingredient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE food_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ingredient
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE meal
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE meal_place_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media_place_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media_media_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE media_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place_place_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE place_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip_country
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip_trip_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip_tag
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_
        SQL);
    }
}
