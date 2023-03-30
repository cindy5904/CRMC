<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330091254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication ADD publication_user_id INT DEFAULT NULL, ADD publication_company_id INT DEFAULT NULL, ADD publication_formation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779AFE705A9 FOREIGN KEY (publication_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779F9EC5F37 FOREIGN KEY (publication_company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677913D331EB FOREIGN KEY (publication_formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_AF3C6779AFE705A9 ON publication (publication_user_id)');
        $this->addSql('CREATE INDEX IDX_AF3C6779F9EC5F37 ON publication (publication_company_id)');
        $this->addSql('CREATE INDEX IDX_AF3C677913D331EB ON publication (publication_formation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779AFE705A9');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779F9EC5F37');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C677913D331EB');
        $this->addSql('DROP INDEX IDX_AF3C6779AFE705A9 ON publication');
        $this->addSql('DROP INDEX IDX_AF3C6779F9EC5F37 ON publication');
        $this->addSql('DROP INDEX IDX_AF3C677913D331EB ON publication');
        $this->addSql('ALTER TABLE publication DROP publication_user_id, DROP publication_company_id, DROP publication_formation_id');
    }
}
