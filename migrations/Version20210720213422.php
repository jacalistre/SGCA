<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720213422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_salud ADD CONSTRAINT FK_2F5AAF084E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
        $this->addSql('CREATE INDEX IDX_2F5AAF084E7121AF ON area_salud (provincia_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE area_salud DROP FOREIGN KEY FK_2F5AAF084E7121AF');
        $this->addSql('DROP INDEX IDX_2F5AAF084E7121AF ON area_salud');
    }
}
