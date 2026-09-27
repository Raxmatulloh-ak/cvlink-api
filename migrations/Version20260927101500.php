<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260927101500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add mandatory built-in profile attributes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO attribute_category (name, display_order, created_at, updated_at)
            SELECT 'Personal', 0, CURRENT_TIMESTAMP, NULL
            WHERE NOT EXISTS (
                SELECT 1 FROM attribute_category WHERE name = 'Personal'
            )");

        $this->addSql("INSERT INTO attribute_definition (category_id, name, description, value_type, builtin_key, version, created_at, updated_at)
            SELECT id, 'First Name', NULL, 'string', 'first_name', 1, CURRENT_TIMESTAMP, NULL
            FROM attribute_category
            WHERE name = 'Personal'
              AND NOT EXISTS (
                  SELECT 1 FROM attribute_definition WHERE builtin_key = 'first_name'
              )");

        $this->addSql("INSERT INTO attribute_definition (category_id, name, description, value_type, builtin_key, version, created_at, updated_at)
            SELECT id, 'Last Name', NULL, 'string', 'last_name', 1, CURRENT_TIMESTAMP, NULL
            FROM attribute_category
            WHERE name = 'Personal'
              AND NOT EXISTS (
                  SELECT 1 FROM attribute_definition WHERE builtin_key = 'last_name'
              )");

        $this->addSql("INSERT INTO attribute_definition (category_id, name, description, value_type, builtin_key, version, created_at, updated_at)
            SELECT id, 'Location', NULL, 'string', 'location', 1, CURRENT_TIMESTAMP, NULL
            FROM attribute_category
            WHERE name = 'Personal'
              AND NOT EXISTS (
                  SELECT 1 FROM attribute_definition WHERE builtin_key = 'location'
              )");

        $this->addSql("INSERT INTO attribute_definition (category_id, name, description, value_type, builtin_key, version, created_at, updated_at)
            SELECT id, 'Personal Photo', NULL, 'image', 'personal_photo', 1, CURRENT_TIMESTAMP, NULL
            FROM attribute_category
            WHERE name = 'Personal'
              AND NOT EXISTS (
                  SELECT 1 FROM attribute_definition WHERE builtin_key = 'personal_photo'
              )");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM attribute_definition
            WHERE builtin_key IN ('first_name', 'last_name', 'location', 'personal_photo')");

        $this->addSql("DELETE FROM attribute_category
            WHERE name = 'Personal'
              AND NOT EXISTS (
                  SELECT 1 FROM attribute_definition WHERE category_id = attribute_category.id
              )");
    }
}
