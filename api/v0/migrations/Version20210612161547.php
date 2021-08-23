<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210612161547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comunidades ADD administrador_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comunidades ADD CONSTRAINT FK_4EC3137648DFEBB7 FOREIGN KEY (administrador_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_4EC3137648DFEBB7 ON comunidades (administrador_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comunidades DROP FOREIGN KEY FK_4EC3137648DFEBB7');
        $this->addSql('DROP INDEX IDX_4EC3137648DFEBB7 ON comunidades');
        $this->addSql('ALTER TABLE comunidades DROP administrador_id');
    }
}
