<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Updates current users to fill in category and divisions fields
 * Changes users.category and users.category fields to not null
 */
class Version20180531191700 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE users SET category = "Bénévole" WHERE is_gla = 0 AND is_volunteer = 1');
        $this->addSql('UPDATE users SET category = "GLA" WHERE is_gla = 1 AND is_volunteer = 0');
        $this->addSql('UPDATE users SET category = "Admin" WHERE is_gla = 1 AND is_volunteer = 1');

        $this->addSql('UPDATE users SET divisions = "Appui GLA"');
        $this->addSql('UPDATE users SET divisions = "Appui GLA, Bricolage, Energie" WHERE category = "GLA"');

        $this->addSql('ALTER TABLE users CHANGE category category VARCHAR(50) NOT NULL, CHANGE divisions divisions LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users CHANGE category category VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE divisions divisions LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:simple_array)\'');
    }
}
