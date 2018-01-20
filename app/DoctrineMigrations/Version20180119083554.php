<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119083554 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('ALTER TABLE stripe_account DROP FOREIGN KEY FK_52F1675E8BAC62AF');
        $this->addSql('DROP INDEX UNIQ_52F1675E8BAC62AF ON stripe_account');
        $this->addSql('ALTER TABLE stripe_account ADD city VARCHAR(255) DEFAULT NOT NULL, ADD day_of_birth VARCHAR(255) DEFAULT NULL, ADD month_of_birth VARCHAR(255) DEFAULT NULL, ADD year_of_birth VARCHAR(255) DEFAULT NULL, DROP city_id, DROP date_of_birth');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_account ADD city_id INT DEFAULT NULL, ADD date_of_birth DATE DEFAULT NULL, DROP city, DROP day_of_birth, DROP month_of_birth, DROP year_of_birth');
        $this->addSql('ALTER TABLE stripe_account ADD CONSTRAINT FK_52F1675E8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_52F1675E8BAC62AF ON stripe_account (city_id)');
    }
}
