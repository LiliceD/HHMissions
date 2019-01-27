<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190127152344 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("
            INSERT INTO building_inspection_item_headers (name, theme, description, `rank`)
            VALUES
            ('Bâti / façades', '3_Etat général', 'Contrôle visuel de l''extérieur du bâtiment\nStores / volets / balcons', 1),
            ('Porte allée Contrôle d''accès', '1_Sécurité', 'Bon fonctionnement de la fermeture', 2),
            ('Etat Hall d''accès', '2_Hygiène_Propreté', '', 3),
            ('Plan d''évacuation', '1_Sécurité', '', 4),
            ('Panneau d''affichage HH', '5_Divers', 'Fermeture / Fixation', 5),
            ('Boîtes aux lettres', '5_Divers', 'Fermeture / Fixation', 6),
            ('Local poubelles', '2_Hygiène_Propreté', 'Nombre de poubelles visibles', 7),
            ('Ascenseur', '4_Equipements techniques', 'Date du dernier contrôle\nAffichage du N° d''appel d''urgence dans la cabine', 8),
            ('Escalier / Paliers / Couloirs', '2_Hygiène_Propreté', 'Evaluation générale de l''état d''entretien', 9),
            ('Fenêtres', '3_Etat général', 'Etat des carreaux\nFermeture / Etanchéité', 10),
            ('Eclairage', '1_Sécurité', 'Bon fonctionnement\nOpportunité de passer en lampes LED', 11),
            ('Portes palières (fermeture / coupe feu)', '1_Sécurité', 'Voir en même temps date contrôle extincteurs', 12),
            ('Portes appartements', '3_Etat général', '', 13),
            ('Eclairage de sécurité (Blocs)', '1_Sécurité', '', 14),
            ('Placards techniques', '5_Divers', 'Fermeture\nEncombrement éventuel', 15),
            ('Eclairage de sécurité (Blocs)', '1_Sécurité', '', 16),
            ('Trappes de désenfumage', '1_Sécurité', 'Fermeture / Etanchéité', 17),
            ('Grenier', '2_Hygiène_Propreté', '', 18),
            ('Terrasses Communes / privatives', '5_Divers', '', 19),
            ('Caves', '2_Hygiène_Propreté', '', 20),
            ('Sous-sol / Garages / Parkings', '2_Hygiène_Propreté', 'Fermeture\nEncombrants', 21),
            ('Chaufferie', '4_Equipements techniques', 'Fermeture accès / Propreté\nMaintenance: dernier passage\nExtincteur, date contrôle inf. 1 an', 22),
            ('Local de production EC collectif', '4_Equipements techniques', '', 23),
            ('Gestion des encombrants', '2_Hygiène_Propreté', 'Importance et localisation des objets\nBesoin d''enlèvement', 24),
            ('Espaces extérieurs / Jardin / Cours', '3_Etat général', 'Entretien / Clôture', 25),
            ('Divers', '6_REX Capitalisation', 'Infos recueillies sur place / Retour d''expérience à communiquer', 26);
        ");

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM building_inspection_item_headers WHERE 1;");
    }
}
