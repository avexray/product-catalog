<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261030211854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_attributes (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, product_id INT NOT NULL, INDEX IDX_A2FCC15B4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_images (id INT AUTO_INCREMENT NOT NULL, url LONGTEXT NOT NULL, path VARCHAR(255) NOT NULL, product_id INT NOT NULL, INDEX IDX_8263FFCE4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, external_code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, discount NUMERIC(5, 2) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5AE20A50B4 (external_code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE product_attributes ADD CONSTRAINT FK_A2FCC15B4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT FK_8263FFCE4584665A FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attributes DROP FOREIGN KEY FK_A2FCC15B4584665A');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY FK_8263FFCE4584665A');
        $this->addSql('DROP TABLE product_attributes');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE products');
    }
}
