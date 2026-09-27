<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260927085756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_option ALTER display_order SET NOT NULL');
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT fk_b66ffe9291bd8781');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE9291BD8781 FOREIGN KEY (candidate_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE cv_like DROP CONSTRAINT fk_cd2da06fcfe419e2');
        $this->addSql('ALTER TABLE cv_like DROP CONSTRAINT fk_cd2da06f156be243');
        $this->addSql('ALTER TABLE cv_like ADD CONSTRAINT FK_CD2DA06FCFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE cv_like ADD CONSTRAINT FK_CD2DA06F156BE243 FOREIGN KEY (recruiter_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT fk_2fb3d0ee91bd8781');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE91BD8781 FOREIGN KEY (candidate_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE user_attribute_value DROP CONSTRAINT fk_ec93d70d7e3c61f9');
        $this->addSql('ALTER TABLE user_attribute_value DROP CONSTRAINT fk_ec93d70db6e62efa');
        $this->addSql('ALTER TABLE user_attribute_value ADD CONSTRAINT FK_EC93D70D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE user_attribute_value ADD CONSTRAINT FK_EC93D70DB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_option ALTER display_order DROP NOT NULL');
        $this->addSql('ALTER TABLE cv DROP CONSTRAINT FK_B66FFE9291BD8781');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT fk_b66ffe9291bd8781 FOREIGN KEY (candidate_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cv_like DROP CONSTRAINT FK_CD2DA06FCFE419E2');
        $this->addSql('ALTER TABLE cv_like DROP CONSTRAINT FK_CD2DA06F156BE243');
        $this->addSql('ALTER TABLE cv_like ADD CONSTRAINT fk_cd2da06fcfe419e2 FOREIGN KEY (cv_id) REFERENCES cv (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cv_like ADD CONSTRAINT fk_cd2da06f156be243 FOREIGN KEY (recruiter_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE91BD8781');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT fk_2fb3d0ee91bd8781 FOREIGN KEY (candidate_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_attribute_value DROP CONSTRAINT FK_EC93D70D7E3C61F9');
        $this->addSql('ALTER TABLE user_attribute_value DROP CONSTRAINT FK_EC93D70DB6E62EFA');
        $this->addSql('ALTER TABLE user_attribute_value ADD CONSTRAINT fk_ec93d70d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_attribute_value ADD CONSTRAINT fk_ec93d70db6e62efa FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
