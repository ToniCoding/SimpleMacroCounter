<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511163642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE kcals_daily CHANGE kcals kcals INT NOT NULL, CHANGE protein protein NUMERIC(5, 2) NOT NULL, CHANGE carbs carbs NUMERIC(5, 2) NOT NULL, CHANGE fats fats NUMERIC(5, 2) NOT NULL, CHANGE fiber fiber NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE user_goals CHANGE calories calories NUMERIC(5, 2) NOT NULL, CHANGE protein protein NUMERIC(5, 2) NOT NULL, CHANGE carbs carbs NUMERIC(5, 2) NOT NULL, CHANGE fats fats NUMERIC(5, 2) NOT NULL, CHANGE fiber fiber NUMERIC(5, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_goals CHANGE calories calories INT NOT NULL, CHANGE protein protein INT NOT NULL, CHANGE carbs carbs INT NOT NULL, CHANGE fats fats INT NOT NULL, CHANGE fiber fiber INT NOT NULL');
        $this->addSql('ALTER TABLE kcals_daily CHANGE kcals kcals NUMERIC(10, 2) DEFAULT NULL, CHANGE protein protein NUMERIC(10, 2) DEFAULT NULL, CHANGE carbs carbs NUMERIC(10, 2) DEFAULT NULL, CHANGE fats fats NUMERIC(10, 2) DEFAULT NULL, CHANGE fiber fiber NUMERIC(10, 2) DEFAULT NULL');
    }
}
