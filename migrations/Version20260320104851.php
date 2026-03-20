<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320104851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, seen TINYINT NOT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, actor_id INT NOT NULL, INDEX IDX_BF5476CA7E3C61F9 (owner_id), INDEX IDX_BF5476CA10DAF24A (actor_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA10DAF24A FOREIGN KEY (actor_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA7E3C61F9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA10DAF24A');
        $this->addSql('DROP TABLE notification');
    }
}
