<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180408103502 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE accomodations CHANGE street street VARCHAR(50) NOT NULL, CHANGE city city VARCHAR(25) NOT NULL, CHANGE name name VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE missions ADD gla_id INT DEFAULT NULL, ADD volunteer_id INT DEFAULT NULL, DROP gla, DROP volunteer, CHANGE status status VARCHAR(25) NOT NULL, CHANGE description description VARCHAR(3000) NOT NULL, CHANGE info info VARCHAR(1000) DEFAULT NULL, CHANGE conclusions conclusions VARCHAR(3000) DEFAULT NULL');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47EE26B8849 FOREIGN KEY (gla_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47E8EFAB6B1 FOREIGN KEY (volunteer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_34F1D47EE26B8849 ON missions (gla_id)');
        $this->addSql('CREATE INDEX IDX_34F1D47E8EFAB6B1 ON missions (volunteer_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE accomodations CHANGE name name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE street street VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE city city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47EE26B8849');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47E8EFAB6B1');
        $this->addSql('DROP INDEX IDX_34F1D47EE26B8849 ON missions');
        $this->addSql('DROP INDEX IDX_34F1D47E8EFAB6B1 ON missions');
        $this->addSql('ALTER TABLE missions ADD gla VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD volunteer VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP gla_id, DROP volunteer_id, CHANGE status status VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE description description LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE info info LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE conclusions conclusions LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
