<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250201081936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment ALTER amount TYPE NUMERIC(5, 2)');
        $this->addSql('ALTER TABLE reservation ADD restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP date');
        $this->addSql('ALTER TABLE reservation ALTER restaurant_table_id DROP NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_42C84955B1E7706E ON reservation (restaurant_id)');
        $this->addSql('ALTER TABLE restaurant ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE restaurant ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN restaurant.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN restaurant.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE restaurant_table ADD is_approved BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE time_slot ALTER start_time TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE time_slot ALTER end_time TYPE TIME(0) WITHOUT TIME ZONE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE time_slot ALTER start_time TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE time_slot ALTER end_time TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C84955B1E7706E');
        $this->addSql('DROP INDEX IDX_42C84955B1E7706E');
        $this->addSql('ALTER TABLE reservation ADD date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE reservation DROP restaurant_id');
        $this->addSql('ALTER TABLE reservation ALTER restaurant_table_id SET NOT NULL');
        $this->addSql('ALTER TABLE restaurant DROP created_at');
        $this->addSql('ALTER TABLE restaurant DROP updated_at');
        $this->addSql('ALTER TABLE payment ALTER amount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE restaurant_table DROP is_approved');
    }
}
