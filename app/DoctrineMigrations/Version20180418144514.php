<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180418144514 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // or update schema
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE erp_user_fee RENAME INDEX idx_e54e5729a76ed395 TO IDX_2EB5E72DA76ED395;');
        $this->addSql('ALTER TABLE invited_users DROP FOREIGN KEY FK_19D0220E549213EC;');
        $this->addSql('ALTER TABLE invited_users ADD CONSTRAINT FK_19D0220E549213EC FOREIGN KEY (property_id) REFERENCES properties (id);');
        $this->addSql('ALTER TABLE pro_requests CHANGE status status VARCHAR(32) DEFAULT NULL;');
        $this->addSql('ALTER TABLE rent_payment_balance DROP INDEX IDX_92582F77A76ED395, ADD UNIQUE INDEX UNIQ_92582F77A76ED395 (user_id);');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9E065F932;');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E065F932 ON users;');
        $this->addSql('ALTER TABLE users DROP stripe_account_id, CHANGE status status VARCHAR(16) DEFAULT NULL, CHANGE is_private_paysimple is_private_paysimple TINYINT(1) NOT NULL, CHANGE paysimple_api_secret_key paysimple_api_secret_key VARCHAR(255) DEFAULT NULL, CHANGE is_property_counter_free is_property_counter_free TINYINT(1) NOT NULL, CHANGE is_application_form_counter_free is_application_form_counter_free TINYINT(1) NOT NULL, CHANGE is_active_monthly_fee is_active_monthly_fee TINYINT(1) NOT NULL;');
        $this->addSql('ALTER TABLE user_documents CHANGE status status VARCHAR(32) DEFAULT NULL;');
        $this->addSql('ALTER TABLE fees_options CHANGE default_email default_email VARCHAR(255) NOT NULL, CHANGE smart_move_enable smart_move_enable TINYINT(1) NOT NULL, CHANGE cc_transaction_fee cc_transaction_fee DOUBLE PRECISION DEFAULT \'1\' NOT NULL, CHANGE application_form_anonymous_fee application_form_anonymous_fee DOUBLE PRECISION DEFAULT \'1\' NOT NULL;');
        $this->addSql('ALTER TABLE application_fields DROP FOREIGN KEY FK_CED6EF4DAFA0D296;');
        $this->addSql('ALTER TABLE application_fields CHANGE type type VARCHAR(16) DEFAULT NULL;');
        $this->addSql('ALTER TABLE application_fields ADD CONSTRAINT FK_CED6EF4DAFA0D296 FOREIGN KEY (application_section_id) REFERENCES application_sections (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE contract_forms RENAME INDEX uniq_property TO UNIQ_BE3E87D3549213EC;');
        $this->addSql('ALTER TABLE contract_sections RENAME INDEX idx_form TO IDX_BE2158675FF69B7D;');
        $this->addSql('ALTER TABLE properties CHANGE status status VARCHAR(16) DEFAULT NULL;');
        $this->addSql('ALTER TABLE property_repost_requests CHANGE status status VARCHAR(16) DEFAULT NULL;');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_c26cde1e9395c3f3 TO IDX_5A607FEC9395C3F3;');
        $this->addSql('ALTER TABLE scheduled_rent_payment RENAME INDEX idx_c26cde1e9b6b5fba TO IDX_5A607FEC9B6B5FBA;');
        $this->addSql('ALTER TABLE homepage_slides CHANGE image_id image_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE ps_deferred_payments CHANGE status status VARCHAR(16) DEFAULT NULL;');
        $this->addSql('ALTER TABLE ps_history CHANGE status status VARCHAR(16) DEFAULT NULL, CHANGE transfer_date transfer_date DATETIME NOT NULL;');
        $this->addSql('ALTER TABLE ps_recurring_payment CHANGE status status VARCHAR(16) DEFAULT NULL;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
