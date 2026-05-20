<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518014616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE following (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, followed_id INT NOT NULL, INDEX IDX_71BF8DE3A76ED395 (user_id), INDEX IDX_71BF8DE3D956F010 (followed_id), UNIQUE INDEX uniq_user_followed (user_id, followed_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, publication_id INT NOT NULL, INDEX IDX_AC6340B3A76ED395 (user_id), INDEX IDX_AC6340B338B217A7 (publication_id), UNIQUE INDEX unique_like (user_id, publication_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, type_id INT DEFAULT NULL, readed VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL, extra VARCHAR(100) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE private_messages (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, readed VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL, emitter INT NOT NULL, receiver INT NOT NULL, INDEX IDX_7C94C13B758ECD31 (emitter), INDEX IDX_7C94C13B3DB88C96 (receiver), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE publications (id INT AUTO_INCREMENT NOT NULL, text LONGTEXT DEFAULT NULL, document VARCHAR(100) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, status VARCHAR(30) DEFAULT NULL, created_at DATETIME DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_32783AF4A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(20) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, nick VARCHAR(50) DEFAULT NULL, bio VARCHAR(255) DEFAULT NULL, active VARCHAR(2) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9290B2F37 (nick), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE following ADD CONSTRAINT FK_71BF8DE3D956F010 FOREIGN KEY (followed_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE private_messages ADD CONSTRAINT FK_7C94C13B758ECD31 FOREIGN KEY (emitter) REFERENCES users (id)');
        $this->addSql('ALTER TABLE private_messages ADD CONSTRAINT FK_7C94C13B3DB88C96 FOREIGN KEY (receiver) REFERENCES users (id)');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3A76ED395');
        $this->addSql('ALTER TABLE following DROP FOREIGN KEY FK_71BF8DE3D956F010');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE private_messages DROP FOREIGN KEY FK_7C94C13B758ECD31');
        $this->addSql('ALTER TABLE private_messages DROP FOREIGN KEY FK_7C94C13B3DB88C96');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4A76ED395');
        $this->addSql('DROP TABLE following');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE private_messages');
        $this->addSql('DROP TABLE publications');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
