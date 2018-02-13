<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180211185915 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission CHANGE volunteer volunteer VARCHAR(255) DEFAULT NULL, CHANGE info info LONGTEXT DEFAULT NULL, CHANGE conclusions conclusions LONGTEXT DEFAULT NULL, CHANGE date_assigned date_assigned DATETIME DEFAULT NULL, CHANGE date_finished date_finished DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission CHANGE volunteer volunteer VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE info info LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE conclusions conclusions LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE date_assigned date_assigned DATETIME NOT NULL, CHANGE date_finished date_finished DATETIME NOT NULL');
    }
}
