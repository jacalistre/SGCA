<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720063158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingreso ADD paciente_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ingreso ADD CONSTRAINT FK_CC9B241F7310DAD4 FOREIGN KEY (paciente_id) REFERENCES paciente (id)');
        $this->addSql('CREATE INDEX IDX_CC9B241F7310DAD4 ON ingreso (paciente_id)');
        $this->addSql('ALTER TABLE usuario ADD centro_id INT DEFAULT NULL, ADD area_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05D298137A7 FOREIGN KEY (centro_id) REFERENCES centro (id)');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DBD0F409C FOREIGN KEY (area_id) REFERENCES area_salud (id)');
        $this->addSql('CREATE INDEX IDX_2265B05D298137A7 ON usuario (centro_id)');
        $this->addSql('CREATE INDEX IDX_2265B05DBD0F409C ON usuario (area_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingreso DROP FOREIGN KEY FK_CC9B241F7310DAD4');
        $this->addSql('DROP INDEX IDX_CC9B241F7310DAD4 ON ingreso');
        $this->addSql('ALTER TABLE ingreso DROP paciente_id');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05D298137A7');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DBD0F409C');
        $this->addSql('DROP INDEX IDX_2265B05D298137A7 ON usuario');
        $this->addSql('DROP INDEX IDX_2265B05DBD0F409C ON usuario');
        $this->addSql('ALTER TABLE usuario DROP centro_id, DROP area_id');
    }
}
