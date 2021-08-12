<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720071110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paciente ADD provincia_id INT NOT NULL');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95E4E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
        $this->addSql('CREATE INDEX IDX_C6CBA95E4E7121AF ON paciente (provincia_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paciente DROP FOREIGN KEY FK_C6CBA95E4E7121AF');
        $this->addSql('DROP INDEX IDX_C6CBA95E4E7121AF ON paciente');
        $this->addSql('ALTER TABLE paciente DROP provincia_id');
    }
}
