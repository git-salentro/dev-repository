<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413103910 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE erp_notifications_template (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE erp_notification_notification ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE erp_notification_notification ADD CONSTRAINT FK_28513D1E5DA0FB8 FOREIGN KEY (template_id) REFERENCES erp_notifications_template (id)');
        $this->addSql('CREATE INDEX IDX_28513D1E5DA0FB8 ON erp_notification_notification (template_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE erp_notification_notification DROP FOREIGN KEY FK_28513D1E5DA0FB8');
        $this->addSql('DROP TABLE erp_notifications_template');
        $this->addSql('DROP INDEX IDX_28513D1E5DA0FB8 ON erp_notification_notification');
        $this->addSql('ALTER TABLE erp_notification_notification DROP template_id');

    }
}
