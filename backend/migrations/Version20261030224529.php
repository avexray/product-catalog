<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261030224529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import_jobs (id INT AUTO_INCREMENT NOT NULL, original_filename VARCHAR(255) NOT NULL, file_path VARCHAR(1024) NOT NULL, status VARCHAR(32) NOT NULL, total_rows INT NOT NULL, processed_rows INT NOT NULL, successful_rows INT NOT NULL, failed_rows INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, finished_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE import_jobs');
    }
}
