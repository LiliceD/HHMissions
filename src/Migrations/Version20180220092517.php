<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180220092517 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission ADD accomodation_id INT DEFAULT NULL, DROP address');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23CFD70509C FOREIGN KEY (accomodation_id) REFERENCES accomodation (id)');
        $this->addSql('CREATE INDEX IDX_9067F23CFD70509C ON mission (accomodation_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23CFD70509C');
        $this->addSql('DROP INDEX IDX_9067F23CFD70509C ON mission');
        $this->addSql('ALTER TABLE mission ADD address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP accomodation_id');
    }
}
