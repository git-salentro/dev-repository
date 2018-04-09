<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180409080437 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE properties DROP price;');
        $this->addSql('ALTER TABLE property_rent_history DROP FOREIGN KEY FK_993FB62549213EC;');
        $this->addSql('ALTER TABLE property_rent_history ADD CONSTRAINT FK_993FB62549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE properties ADD price DOUBLE PRECISION DEFAULT NULL;');

    }
}
