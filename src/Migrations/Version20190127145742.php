<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127145742 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user RENAME INDEX uniq_1483a5e9f85e0677 TO UNIQ_8D93D649F85E0677');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE mission CHANGE activity activity VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_34f1d47efd70509c TO IDX_9067F23CF5B7AF75');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_34f1d47ee26b8849 TO IDX_9067F23CE26B8849');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_34f1d47e8efab6b1 TO IDX_9067F23C8EFAB6B1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mission CHANGE activity activity VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_9067f23cf5b7af75 TO IDX_34F1D47EFD70509C');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_9067f23c8efab6b1 TO IDX_34F1D47E8EFAB6B1');
        $this->addSql('ALTER TABLE mission RENAME INDEX idx_9067f23ce26b8849 TO IDX_34F1D47EE26B8849');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_1483A5E9E7927C74');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649f85e0677 TO UNIQ_1483A5E9F85E0677');
    }
}
