<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330091256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apply ADD apply_publication_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1F23E8AEFF FOREIGN KEY (apply_publication_id) REFERENCES publication (id)');
        $this->addSql('CREATE INDEX IDX_BD2F8C1F23E8AEFF ON apply (apply_publication_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1F23E8AEFF');
        $this->addSql('DROP INDEX IDX_BD2F8C1F23E8AEFF ON apply');
        $this->addSql('ALTER TABLE apply DROP apply_publication_id');
    }
}
