<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260921191325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Unique: user value, cv, cvLike records';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT fk_b66ffe92dd842e46');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX uniq_candidate_cv ON cv (candidate_id, position_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_cv_like ON cv_like (cv_id, recruiter_id)');
        $this->addSql('ALTER TABLE position_attribute ALTER display_order SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_attribute ON user_attribute_value (owner_id, attribute_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT FK_B66FFE92DD842E46');
        $this->addSql('DROP INDEX uniq_candidate_cv');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT fk_b66ffe92dd842e46 FOREIGN KEY (position_id) REFERENCES "position" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX uniq_cv_like');
        $this->addSql('ALTER TABLE position_attribute ALTER display_order DROP NOT NULL');
        $this->addSql('DROP INDEX uniq_user_attribute');
    }
}
