<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260920111324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ORM Version tag added';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_option DROP CONSTRAINT fk_78672eeab6e62efa');
        $this->addSql('ALTER TABLE attribute_option ADD CONSTRAINT FK_78672EEAB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE cv ALTER version SET DEFAULT 1');
        $this->addSql('ALTER TABLE "position" ALTER version SET DEFAULT 1');
        $this->addSql('ALTER TABLE project ALTER version SET DEFAULT 1');
        $this->addSql('ALTER TABLE user_attribute_value ALTER version SET DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_option DROP CONSTRAINT FK_78672EEAB6E62EFA');
        $this->addSql('ALTER TABLE attribute_option ADD CONSTRAINT fk_78672eeab6e62efa FOREIGN KEY (attribute_id) REFERENCES attribute_definition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cv ALTER version DROP DEFAULT');
        $this->addSql('ALTER TABLE position ALTER version DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER version DROP DEFAULT');
        $this->addSql('ALTER TABLE user_attribute_value ALTER version DROP DEFAULT');
    }
}
