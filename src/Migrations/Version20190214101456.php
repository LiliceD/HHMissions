<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190214101456 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE address ADD gla_id INT NOT NULL, ADD referent_id INT DEFAULT NULL');
        $this->addSql('UPDATE address SET gla_id = (SELECT id FROM user WHERE category = "GLA" AND is_active LIMIT 1) WHERE 1');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81E26B8849 FOREIGN KEY (gla_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8135E47E35 FOREIGN KEY (referent_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D4E6F81E26B8849 ON address (gla_id)');
        $this->addSql('CREATE INDEX IDX_D4E6F8135E47E35 ON address (referent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81E26B8849');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8135E47E35');
        $this->addSql('DROP INDEX IDX_D4E6F81E26B8849 ON address');
        $this->addSql('DROP INDEX IDX_D4E6F8135E47E35 ON address');
        $this->addSql('ALTER TABLE address DROP gla_id, DROP referent_id');
    }
}
