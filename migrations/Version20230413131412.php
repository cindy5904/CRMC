<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413131412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apply ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE apply ADD CONSTRAINT FK_BD2F8C1FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BD2F8C1FA76ED395 ON apply (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AD512FDE');
        $this->addSql('DROP INDEX IDX_8D93D649AD512FDE ON user');
        $this->addSql('ALTER TABLE user DROP user_apply_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apply DROP FOREIGN KEY FK_BD2F8C1FA76ED395');
        $this->addSql('DROP INDEX IDX_BD2F8C1FA76ED395 ON apply');
        $this->addSql('ALTER TABLE apply DROP user_id');
        $this->addSql('ALTER TABLE user ADD user_apply_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AD512FDE FOREIGN KEY (user_apply_id) REFERENCES apply (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649AD512FDE ON user (user_apply_id)');
    }
}
