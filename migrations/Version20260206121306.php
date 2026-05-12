<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260206121306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_token CHANGE expires_at expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE access_token RENAME INDEX value TO UNIQ_B6A2DD681D775834');
        $this->addSql('ALTER TABLE access_token RENAME INDEX fk_access_token_user TO IDX_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE kcals_daily CHANGE user_id user_id INT NOT NULL, CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE kcals kcals INT NOT NULL, CHANGE protein protein INT NOT NULL, CHANGE carbs carbs INT NOT NULL, CHANGE fats fats INT NOT NULL, CHANGE fiber fiber INT NOT NULL');
        $this->addSql('ALTER TABLE kcals_daily RENAME INDEX fk_kcals_user TO IDX_86191313A76ED395');
        $this->addSql('ALTER TABLE products CHANGE productName product_name VARCHAR(75) NOT NULL');
        $this->addSql('ALTER TABLE user_goals CHANGE user_id user_id INT NOT NULL, CHANGE calories calories INT NOT NULL, CHANGE protein protein INT NOT NULL, CHANGE carbs carbs INT NOT NULL, CHANGE fats fats INT NOT NULL, CHANGE fiber fiber INT NOT NULL, CHANGE date_time date_time DATETIME NOT NULL');
        $this->addSql('DROP INDEX uniq_email ON users');
        $this->addSql('DROP INDEX uniq_username ON users');
        $this->addSql('ALTER TABLE users ADD timezone VARCHAR(50) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE user_alias user_alias VARCHAR(255) NOT NULL, CHANGE status status VARCHAR(10) NOT NULL, CHANGE created_time created_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_token CHANGE expires_at expires_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE access_token RENAME INDEX uniq_b6a2dd681d775834 TO value');
        $this->addSql('ALTER TABLE access_token RENAME INDEX idx_b6a2dd68a76ed395 TO fk_access_token_user');
        $this->addSql('ALTER TABLE users DROP timezone, CHANGE password password VARCHAR(255) DEFAULT \'\' NOT NULL, CHANGE user_alias user_alias VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(10) DEFAULT \'active\' NOT NULL, CHANGE created_time created_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_email ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_username ON users (username)');
        $this->addSql('ALTER TABLE user_goals CHANGE user_id user_id INT DEFAULT 0 NOT NULL, CHANGE calories calories INT DEFAULT 0 NOT NULL, CHANGE protein protein INT DEFAULT 0 NOT NULL, CHANGE carbs carbs INT DEFAULT 0 NOT NULL, CHANGE fats fats INT DEFAULT 0 NOT NULL, CHANGE fiber fiber INT DEFAULT 0 NOT NULL, CHANGE date_time date_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE products CHANGE product_name productName VARCHAR(75) NOT NULL');
        $this->addSql('ALTER TABLE kcals_daily CHANGE user_id user_id INT DEFAULT 0 NOT NULL, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE kcals kcals INT DEFAULT 0 NOT NULL, CHANGE protein protein INT DEFAULT 0 NOT NULL, CHANGE carbs carbs INT DEFAULT 0 NOT NULL, CHANGE fats fats INT DEFAULT 0 NOT NULL, CHANGE fiber fiber INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE kcals_daily RENAME INDEX idx_86191313a76ed395 TO fk_kcals_user');
    }
}
