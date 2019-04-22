<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190415180019 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission 
            ADD content_updated_at DATETIME, 
            ADD content_updated_by_id INT');
        $this->addSql('UPDATE mission 
            SET content_updated_at = date_created, 
                content_updated_by_id = gla_id 
            WHERE 1');
        $this->addSql('ALTER TABLE mission 
            CHANGE content_updated_at content_updated_at DATETIME NOT NULL, 
            CHANGE content_updated_by_id content_updated_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE mission 
            ADD CONSTRAINT FK_9067F23C907E1793 FOREIGN KEY (content_updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9067F23C907E1793 ON mission (content_updated_by_id)');
        $this->addSql('ALTER TABLE user ADD last_login DATETIME');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C907E1793');
        $this->addSql('DROP INDEX IDX_9067F23C907E1793 ON mission');
        $this->addSql('ALTER TABLE mission DROP content_updated_at, DROP content_updated_by_id');
        $this->addSql('ALTER TABLE user DROP last_login');
    }
}
