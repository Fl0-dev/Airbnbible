<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220328143444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bed (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, nb_place INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bed_room (id INT AUTO_INCREMENT NOT NULL, bed_id INT NOT NULL, room_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_549F803C88688BB9 (bed_id), INDEX IDX_549F803C54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, housing_id INT NOT NULL, entry_date DATETIME NOT NULL, exit_date DATETIME NOT NULL, created_at DATETIME NOT NULL, modified_at DATETIME NOT NULL, total_price INT NOT NULL, INDEX IDX_E00CEDDE19EB6921 (client_id), INDEX IDX_E00CEDDEAD5873E3 (housing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE housing (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, category_id INT NOT NULL, adress VARCHAR(255) NOT NULL, postal_code INT NOT NULL, available_places INT NOT NULL, daily_price INT NOT NULL, is_visible TINYINT(1) NOT NULL, city VARCHAR(255) NOT NULL, INDEX IDX_FB8142C37E3C61F9 (owner_id), INDEX IDX_FB8142C312469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE housing_equipment (housing_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_4A001DAAD5873E3 (housing_id), INDEX IDX_4A001DA517FE9FE (equipment_id), PRIMARY KEY(housing_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE housing_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, housing_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_14B78418AD5873E3 (housing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, housing_id INT NOT NULL, size INT NOT NULL, INDEX IDX_729F519BAD5873E3 (housing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(10) NOT NULL, global_note DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, modified_at DATETIME DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bed_room ADD CONSTRAINT FK_549F803C88688BB9 FOREIGN KEY (bed_id) REFERENCES bed (id)');
        $this->addSql('ALTER TABLE bed_room ADD CONSTRAINT FK_549F803C54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE19EB6921 FOREIGN KEY (client_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id)');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C37E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C312469DE2 FOREIGN KEY (category_id) REFERENCES housing_category (id)');
        $this->addSql('ALTER TABLE housing_equipment ADD CONSTRAINT FK_4A001DAAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE housing_equipment ADD CONSTRAINT FK_4A001DA517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418AD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bed_room DROP FOREIGN KEY FK_549F803C88688BB9');
        $this->addSql('ALTER TABLE housing_equipment DROP FOREIGN KEY FK_4A001DA517FE9FE');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEAD5873E3');
        $this->addSql('ALTER TABLE housing_equipment DROP FOREIGN KEY FK_4A001DAAD5873E3');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418AD5873E3');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BAD5873E3');
        $this->addSql('ALTER TABLE housing DROP FOREIGN KEY FK_FB8142C312469DE2');
        $this->addSql('ALTER TABLE bed_room DROP FOREIGN KEY FK_549F803C54177093');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE19EB6921');
        $this->addSql('ALTER TABLE housing DROP FOREIGN KEY FK_FB8142C37E3C61F9');
        $this->addSql('DROP TABLE bed');
        $this->addSql('DROP TABLE bed_room');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE housing');
        $this->addSql('DROP TABLE housing_equipment');
        $this->addSql('DROP TABLE housing_category');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
