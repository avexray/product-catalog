<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261101193233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import_job_errors (id INT AUTO_INCREMENT NOT NULL, source_row INT NOT NULL, error_text LONGTEXT NOT NULL, import_job_id INT NOT NULL, INDEX IDX_AEDF408F77D1F4B1 (import_job_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE import_job_errors ADD CONSTRAINT FK_AEDF408F77D1F4B1 FOREIGN KEY (import_job_id) REFERENCES import_jobs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import_job_errors DROP FOREIGN KEY FK_AEDF408F77D1F4B1');
        $this->addSql('DROP TABLE import_job_errors');
    }
}
