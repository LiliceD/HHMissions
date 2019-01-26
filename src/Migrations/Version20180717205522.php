<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Updates current missions to fill in division field
 * Changes field missions.division to not null
 */
final class Version20180717205522 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    	$this->addSql('UPDATE missions SET division = "Appui GLA"');

    	$this->addSql('ALTER TABLE missions CHANGE division division VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
    	$this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    	 $this->addSql('ALTER TABLE missions CHANGE division division VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
