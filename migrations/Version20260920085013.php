<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260920085013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tables minor mistakes fixed';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_definition ALTER description DROP NOT NULL');
        $this->addSql('ALTER TABLE attribute_definition ALTER version SET DEFAULT 1');
        $this->addSql('ALTER TABLE attribute_definition RENAME COLUMN bultin_key TO builtin_key');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C5628BD5E237E06 ON attribute_definition (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C5628BDF3BA5C0B ON attribute_definition (builtin_key)');
        $this->addSql('ALTER TABLE position_access_rule RENAME COLUMN boolen_operand TO boolean_operand');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE user_attribute_value RENAME COLUMN nemeric_value TO numeric_value');
        $this->addSql('ALTER TABLE user_attribute_value RENAME COLUMN boolen_value TO boolean_value');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_6C5628BD5E237E06');
        $this->addSql('DROP INDEX UNIQ_6C5628BDF3BA5C0B');
        $this->addSql('ALTER TABLE attribute_definition ALTER description SET NOT NULL');
        $this->addSql('ALTER TABLE attribute_definition ALTER version DROP DEFAULT');
        $this->addSql('ALTER TABLE attribute_definition RENAME COLUMN builtin_key TO bultin_key');
        $this->addSql('ALTER TABLE position_access_rule RENAME COLUMN boolean_operand TO boolen_operand');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('ALTER TABLE user_attribute_value RENAME COLUMN numeric_value TO nemeric_value');
        $this->addSql('ALTER TABLE user_attribute_value RENAME COLUMN boolean_value TO boolen_value');
    }
}
