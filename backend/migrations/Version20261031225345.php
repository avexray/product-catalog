<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261031225345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attributes ADD attribute_key VARCHAR(255) NOT NULL, ADD attribute_value VARCHAR(255) NOT NULL, DROP `key`, DROP value');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attributes ADD `key` VARCHAR(255) NOT NULL, ADD value VARCHAR(255) NOT NULL, DROP attribute_key, DROP attribute_value');
    }
}
