<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260627130716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, user_alias VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, age INT NOT NULL, status VARCHAR(10) NOT NULL, created_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', timezone VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id) )DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, value VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B6A2DD681D775834 (value), INDEX IDX_B6A2DD68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE allowed_markets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE foods (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, protein NUMERIC(8, 2) NOT NULL, carbs NUMERIC(8, 2) NOT NULL, fats NUMERIC(8, 2) NOT NULL, fiber NUMERIC(8, 2) NOT NULL, market VARCHAR(255) NOT NULL, INDEX IDX_3803909DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kcals_daily (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', kcals INT NOT NULL, protein NUMERIC(8, 2) NOT NULL, carbs NUMERIC(8, 2) NOT NULL, fats NUMERIC(8, 2) NOT NULL, fiber NUMERIC(8, 2) NOT NULL, INDEX IDX_86191313A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(500) NOT NULL, market VARCHAR(500) NOT NULL, brand VARCHAR(500) NOT NULL, kcal INT NOT NULL, protein NUMERIC(10, 2) NOT NULL, carbs NUMERIC(10, 2) NOT NULL, fats NUMERIC(10, 2) NOT NULL, fiber NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_goals (user_id INT NOT NULL, calories INT NOT NULL, protein NUMERIC(8, 2) NOT NULL, carbs NUMERIC(8, 2) NOT NULL, fats NUMERIC(8, 2) NOT NULL, fiber NUMERIC(8, 2) NOT NULL, date_time DATETIME NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_metrics (user_id INT NOT NULL, creatine_streak INT NOT NULL, experience INT NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE foods ADD CONSTRAINT FK_3803909DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE kcals_daily ADD CONSTRAINT FK_86191313A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_goals ADD CONSTRAINT FK_25E6E577A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_metrics ADD CONSTRAINT FK_7A87B4EDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE FULLTEXT INDEX ft_product_name_market_brand ON products (product_name, market, brand)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE foods DROP FOREIGN KEY FK_3803909DA76ED395');
        $this->addSql('ALTER TABLE kcals_daily DROP FOREIGN KEY FK_86191313A76ED395');
        $this->addSql('ALTER TABLE user_goals DROP FOREIGN KEY FK_25E6E577A76ED395');
        $this->addSql('ALTER TABLE user_metrics DROP FOREIGN KEY FK_7A87B4EDA76ED395');
        $this->addSql('DROP INDEX ft_product_name_market_brand ON products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE allowed_markets');
        $this->addSql('DROP TABLE foods');
        $this->addSql('DROP TABLE kcals_daily');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE user_goals');
        $this->addSql('DROP TABLE user_metrics');
    }
}
