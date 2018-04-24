<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180424075407 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE accomodations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) DEFAULT NULL, street VARCHAR(50) NOT NULL, postal_code INT NOT NULL, city VARCHAR(25) NOT NULL, owner_type VARCHAR(255) DEFAULT NULL, access VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE missions (id INT AUTO_INCREMENT NOT NULL, accomodation_id INT NOT NULL, gla_id INT NOT NULL, volunteer_id INT DEFAULT NULL, status VARCHAR(25) NOT NULL, description VARCHAR(3000) NOT NULL, info VARCHAR(1000) DEFAULT NULL, conclusions VARCHAR(3000) DEFAULT NULL, date_created DATE NOT NULL, date_assigned DATE DEFAULT NULL, date_finished DATE DEFAULT NULL, attachment VARCHAR(255) DEFAULT NULL, INDEX IDX_34F1D47EFD70509C (accomodation_id), INDEX IDX_34F1D47EE26B8849 (gla_id), INDEX IDX_34F1D47E8EFAB6B1 (volunteer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', name VARCHAR(50) NOT NULL, is_active TINYINT(1) NOT NULL, is_gla TINYINT(1) NOT NULL, is_volunteer TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47EFD70509C FOREIGN KEY (accomodation_id) REFERENCES accomodations (id)');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47EE26B8849 FOREIGN KEY (gla_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47E8EFAB6B1 FOREIGN KEY (volunteer_id) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47EFD70509C');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47EE26B8849');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47E8EFAB6B1');
        $this->addSql('DROP TABLE accomodations');
        $this->addSql('DROP TABLE missions');
        $this->addSql('DROP TABLE users');
    }
}
