<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720062642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE area_salud (id INT AUTO_INCREMENT NOT NULL, municipio_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX IDX_2F5AAF0858BC1BE0 (municipio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cama (id INT AUTO_INCREMENT NOT NULL, sala_id INT NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, numero INT NOT NULL, estado VARCHAR(255) NOT NULL, INDEX IDX_AB2462C2C51CDF3F (sala_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE centro (id INT AUTO_INCREMENT NOT NULL, municipio_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, tipo VARCHAR(255) NOT NULL, INDEX IDX_2675036B58BC1BE0 (municipio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingreso (id INT AUTO_INCREMENT NOT NULL, usuario_id INT DEFAULT NULL, centro_id INT NOT NULL, fecha_entrada DATETIME DEFAULT NULL, fecha_salida DATETIME DEFAULT NULL, estado VARCHAR(255) NOT NULL, fecha_confirmacion DATETIME NOT NULL, INDEX IDX_CC9B241FDB38439E (usuario_id), INDEX IDX_CC9B241F298137A7 (centro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE municipio (id INT AUTO_INCREMENT NOT NULL, provincia_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, INDEX IDX_FE98F5E04E7121AF (provincia_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paciente (id INT AUTO_INCREMENT NOT NULL, municipio_id INT NOT NULL, area_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, apellidos VARCHAR(255) NOT NULL, carnet VARCHAR(11) DEFAULT NULL, pasaporte VARCHAR(255) DEFAULT NULL, edad DOUBLE PRECISION NOT NULL, sexo TINYINT(1) NOT NULL, color VARCHAR(255) NOT NULL, direccion_ci VARCHAR(255) NOT NULL, direccion_res VARCHAR(255) DEFAULT NULL, epidemiologia VARCHAR(255) NOT NULL, sintomatologia VARCHAR(255) NOT NULL, riesgo VARCHAR(255) NOT NULL, transportable VARCHAR(255) NOT NULL, vacuna VARCHAR(255) DEFAULT NULL, dosis VARCHAR(255) DEFAULT NULL, hta TINYINT(1) DEFAULT NULL, dm TINYINT(1) DEFAULT NULL, epoc TINYINT(1) DEFAULT NULL, ab TINYINT(1) DEFAULT NULL, obeso TINYINT(1) DEFAULT NULL, ci TINYINT(1) DEFAULT NULL, vih TINYINT(1) DEFAULT NULL, trastornos TINYINT(1) DEFAULT NULL, inmunodeprimido TINYINT(1) DEFAULT NULL, cancer TINYINT(1) DEFAULT NULL, otros TINYINT(1) DEFAULT NULL, fc INT NOT NULL, fr INT NOT NULL, ta VARCHAR(7) NOT NULL, saturacion INT DEFAULT NULL, observaciones LONGTEXT DEFAULT NULL, INDEX IDX_C6CBA95E58BC1BE0 (municipio_id), INDEX IDX_C6CBA95EBD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provincia (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prueba (id INT AUTO_INCREMENT NOT NULL, ingreso_id INT NOT NULL, fecha DATE NOT NULL, tipo VARCHAR(255) NOT NULL, resultado VARCHAR(255) NOT NULL, INDEX IDX_46711E43E70E8ADB (ingreso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sala (id INT AUTO_INCREMENT NOT NULL, centro_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) DEFAULT NULL, INDEX IDX_E226041C298137A7 (centro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE area_salud ADD CONSTRAINT FK_2F5AAF0858BC1BE0 FOREIGN KEY (municipio_id) REFERENCES municipio (id)');
        $this->addSql('ALTER TABLE cama ADD CONSTRAINT FK_AB2462C2C51CDF3F FOREIGN KEY (sala_id) REFERENCES sala (id)');
        $this->addSql('ALTER TABLE centro ADD CONSTRAINT FK_2675036B58BC1BE0 FOREIGN KEY (municipio_id) REFERENCES municipio (id)');
        $this->addSql('ALTER TABLE ingreso ADD CONSTRAINT FK_CC9B241FDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE ingreso ADD CONSTRAINT FK_CC9B241F298137A7 FOREIGN KEY (centro_id) REFERENCES centro (id)');
        $this->addSql('ALTER TABLE municipio ADD CONSTRAINT FK_FE98F5E04E7121AF FOREIGN KEY (provincia_id) REFERENCES provincia (id)');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95E58BC1BE0 FOREIGN KEY (municipio_id) REFERENCES municipio (id)');
        $this->addSql('ALTER TABLE paciente ADD CONSTRAINT FK_C6CBA95EBD0F409C FOREIGN KEY (area_id) REFERENCES area_salud (id)');
        $this->addSql('ALTER TABLE prueba ADD CONSTRAINT FK_46711E43E70E8ADB FOREIGN KEY (ingreso_id) REFERENCES ingreso (id)');
        $this->addSql('ALTER TABLE sala ADD CONSTRAINT FK_E226041C298137A7 FOREIGN KEY (centro_id) REFERENCES centro (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paciente DROP FOREIGN KEY FK_C6CBA95EBD0F409C');
        $this->addSql('ALTER TABLE ingreso DROP FOREIGN KEY FK_CC9B241F298137A7');
        $this->addSql('ALTER TABLE sala DROP FOREIGN KEY FK_E226041C298137A7');
        $this->addSql('ALTER TABLE prueba DROP FOREIGN KEY FK_46711E43E70E8ADB');
        $this->addSql('ALTER TABLE area_salud DROP FOREIGN KEY FK_2F5AAF0858BC1BE0');
        $this->addSql('ALTER TABLE centro DROP FOREIGN KEY FK_2675036B58BC1BE0');
        $this->addSql('ALTER TABLE paciente DROP FOREIGN KEY FK_C6CBA95E58BC1BE0');
        $this->addSql('ALTER TABLE municipio DROP FOREIGN KEY FK_FE98F5E04E7121AF');
        $this->addSql('ALTER TABLE cama DROP FOREIGN KEY FK_AB2462C2C51CDF3F');
        $this->addSql('DROP TABLE area_salud');
        $this->addSql('DROP TABLE cama');
        $this->addSql('DROP TABLE centro');
        $this->addSql('DROP TABLE ingreso');
        $this->addSql('DROP TABLE municipio');
        $this->addSql('DROP TABLE paciente');
        $this->addSql('DROP TABLE provincia');
        $this->addSql('DROP TABLE prueba');
        $this->addSql('DROP TABLE sala');
    }
}
