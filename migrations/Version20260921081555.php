<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260921081555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ON DELETE CASCADE at Position Access Rule table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX uniq_option ON attribute_option (attribute_id, label)');
        $this->addSql('ALTER TABLE position_access_rule DROP CONSTRAINT fk_5d97a8c6b6e62efa');
        $this->addSql('ALTER TABLE position_access_rule DROP CONSTRAINT fk_5d97a8c6dd842e46');
        $this->addSql('ALTER TABLE position_access_rule ALTER numeric_operand TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE position_access_rule ADD CONSTRAINT FK_5D97A8C6B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE position_access_rule ADD CONSTRAINT FK_5D97A8C6DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE position_attribute DROP CONSTRAINT fk_af5bee86b6e62efa');
        $this->addSql('ALTER TABLE position_attribute DROP CONSTRAINT fk_af5bee86dd842e46');
        $this->addSql('ALTER TABLE position_attribute ADD CONSTRAINT FK_AF5BEE86B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE position_attribute ADD CONSTRAINT FK_AF5BEE86DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('CREATE UNIQUE INDEX uniq_position_attribute ON position_attribute (position_id, attribute_id)');
        $this->addSql('ALTER TABLE user_attribute_value ALTER numeric_value TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_option');
        $this->addSql('ALTER TABLE position_access_rule DROP CONSTRAINT FK_5D97A8C6DD842E46');
        $this->addSql('ALTER TABLE position_access_rule DROP CONSTRAINT FK_5D97A8C6B6E62EFA');
        $this->addSql('ALTER TABLE position_access_rule ALTER numeric_operand TYPE INT');
        $this->addSql('ALTER TABLE position_access_rule ADD CONSTRAINT fk_5d97a8c6dd842e46 FOREIGN KEY (position_id) REFERENCES "position" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position_access_rule ADD CONSTRAINT fk_5d97a8c6b6e62efa FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position_attribute DROP CONSTRAINT FK_AF5BEE86DD842E46');
        $this->addSql('ALTER TABLE position_attribute DROP CONSTRAINT FK_AF5BEE86B6E62EFA');
        $this->addSql('DROP INDEX uniq_position_attribute');
        $this->addSql('ALTER TABLE position_attribute ADD CONSTRAINT fk_af5bee86dd842e46 FOREIGN KEY (position_id) REFERENCES "position" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE position_attribute ADD CONSTRAINT fk_af5bee86b6e62efa FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_attribute_value ALTER numeric_value TYPE INT');
    }
}
