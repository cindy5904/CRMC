<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330091347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_competence (user_id INT NOT NULL, competence_id INT NOT NULL, INDEX IDX_33B3AE93A76ED395 (user_id), INDEX IDX_33B3AE9315761DAB (competence_id), PRIMARY KEY(user_id, competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_diplome (user_id INT NOT NULL, diplome_id INT NOT NULL, INDEX IDX_B3415344A76ED395 (user_id), INDEX IDX_B341534426F859E2 (diplome_id), PRIMARY KEY(user_id, diplome_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_competence ADD CONSTRAINT FK_33B3AE93A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_competence ADD CONSTRAINT FK_33B3AE9315761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_diplome ADD CONSTRAINT FK_B3415344A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_diplome ADD CONSTRAINT FK_B341534426F859E2 FOREIGN KEY (diplome_id) REFERENCES diplome (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD user_entreprise_id INT DEFAULT NULL, ADD user_formation_id INT DEFAULT NULL, ADD user_apply_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494A2002BA FOREIGN KEY (user_entreprise_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D2CC542C FOREIGN KEY (user_formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AD512FDE FOREIGN KEY (user_apply_id) REFERENCES apply (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494A2002BA ON user (user_entreprise_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D2CC542C ON user (user_formation_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AD512FDE ON user (user_apply_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_competence DROP FOREIGN KEY FK_33B3AE93A76ED395');
        $this->addSql('ALTER TABLE user_competence DROP FOREIGN KEY FK_33B3AE9315761DAB');
        $this->addSql('ALTER TABLE user_diplome DROP FOREIGN KEY FK_B3415344A76ED395');
        $this->addSql('ALTER TABLE user_diplome DROP FOREIGN KEY FK_B341534426F859E2');
        $this->addSql('DROP TABLE user_competence');
        $this->addSql('DROP TABLE user_diplome');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494A2002BA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D2CC542C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AD512FDE');
        $this->addSql('DROP INDEX IDX_8D93D6494A2002BA ON user');
        $this->addSql('DROP INDEX IDX_8D93D649D2CC542C ON user');
        $this->addSql('DROP INDEX IDX_8D93D649AD512FDE ON user');
        $this->addSql('ALTER TABLE user DROP user_entreprise_id, DROP user_formation_id, DROP user_apply_id');
    }
}
