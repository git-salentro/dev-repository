<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180314153944 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_87C331C7A76ED395');
        $this->addSql('ALTER TABLE stripe_transactions ADD balance_history_id INT DEFAULT NULL;');
        $this->addSql('ALTER TABLE stripe_transactions ADD CONSTRAINT FK_264775DC46A68270 FOREIGN KEY (balance_history_id) REFERENCES balance_history (id) ON DELETE CASCADE;');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_264775DC46A68270 ON stripe_transactions (balance_history_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stripe_transactions DROP FOREIGN KEY FK_264775DC46A68270;');
        $this->addSql('DROP INDEX UNIQ_264775DC46A68270 ON stripe_transactions;');
        $this->addSql('ALTER TABLE stripe_transactions DROP balance_history_id;');


    }
}
