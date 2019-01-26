<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Renames tables accomodations, missions and users to address, mission and user respectively
 */
class Version20180812135905 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47EFD70509C');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47E8EFAB6B1');
        $this->addSql('ALTER TABLE missions DROP FOREIGN KEY FK_34F1D47EE26B8849');
        $this->addSql('ALTER TABLE accomodations RENAME TO address');
        $this->addSql('ALTER TABLE missions RENAME TO mission');
        $this->addSql('ALTER TABLE users RENAME TO user');
        $this->addSql('ALTER TABLE mission CHANGE accomodation_id address_id INT NOT NULL');
        $this->addSql('ALTER TABLE address CHANGE postal_code zip_code INT NOT NULL');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT address_fk FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT gla_fk FOREIGN KEY (gla_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT volunteer_fk FOREIGN KEY (volunteer_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY address_fk ');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY gla_fk ');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY volunteer_fk ');
        $this->addSql('ALTER TABLE address RENAME TO accomodations');
        $this->addSql('ALTER TABLE mission RENAME TO missions');
        $this->addSql('ALTER TABLE user RENAME TO users');
        $this->addSql('ALTER TABLE missions CHANGE address_id accomodation_id INT NOT NULL');
        $this->addSql('ALTER TABLE accomodations CHANGE zip_code postal_code INT NOT NULL');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47E8EFAB6B1 FOREIGN KEY (volunteer_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47EE26B8849 FOREIGN KEY (gla_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE missions ADD CONSTRAINT FK_34F1D47EFD70509C FOREIGN KEY (accomodation_id) REFERENCES accomodations (id)');
    }
}
