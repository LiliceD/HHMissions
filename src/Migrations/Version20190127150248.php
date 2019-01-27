<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127150248 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE building_inspection (id INT AUTO_INCREMENT NOT NULL, gla_id INT NOT NULL, referent_id INT NOT NULL, inspector_id INT NOT NULL, address_id INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_D7B95B00E26B8849 (gla_id), INDEX IDX_D7B95B0035E47E35 (referent_id), INDEX IDX_D7B95B00D0E3F35F (inspector_id), INDEX IDX_D7B95B00F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building_inspection_item_headers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, theme VARCHAR(30) NOT NULL, description VARCHAR(255) DEFAULT NULL, rank INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE building_inspection_item (id INT AUTO_INCREMENT NOT NULL, headers_id INT NOT NULL, inspection_id INT NOT NULL, comment VARCHAR(255) DEFAULT NULL, action VARCHAR(25) DEFAULT NULL, decision_maker VARCHAR(25) DEFAULT NULL, INDEX IDX_5AC44BE16F571EDC (headers_id), INDEX IDX_5AC44BE1F02F2DDF (inspection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE building_inspection ADD CONSTRAINT FK_D7B95B00E26B8849 FOREIGN KEY (gla_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE building_inspection ADD CONSTRAINT FK_D7B95B0035E47E35 FOREIGN KEY (referent_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE building_inspection ADD CONSTRAINT FK_D7B95B00D0E3F35F FOREIGN KEY (inspector_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE building_inspection ADD CONSTRAINT FK_D7B95B00F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE building_inspection_item ADD CONSTRAINT FK_5AC44BE16F571EDC FOREIGN KEY (headers_id) REFERENCES building_inspection_item_headers (id)');
        $this->addSql('ALTER TABLE building_inspection_item ADD CONSTRAINT FK_5AC44BE1F02F2DDF FOREIGN KEY (inspection_id) REFERENCES building_inspection (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE building_inspection_item DROP FOREIGN KEY FK_5AC44BE1F02F2DDF');
        $this->addSql('ALTER TABLE building_inspection_item DROP FOREIGN KEY FK_5AC44BE16F571EDC');
        $this->addSql('DROP TABLE building_inspection');
        $this->addSql('DROP TABLE building_inspection_item_headers');
        $this->addSql('DROP TABLE building_inspection_item');
    }
}
