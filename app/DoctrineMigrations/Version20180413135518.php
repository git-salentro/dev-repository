<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413135518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notifications_template ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE erp_notifications_template ADD CONSTRAINT FK_DDC0FDFBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_DDC0FDFBA76ED395 ON erp_notifications_template (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notifications_template DROP FOREIGN KEY FK_DDC0FDFBA76ED395');
        $this->addSql('DROP INDEX IDX_DDC0FDFBA76ED395 ON erp_notifications_template');
        $this->addSql('ALTER TABLE erp_notifications_template DROP user_id');
    }
}
