<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201001080702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tc_tasks ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tc_tasks ADD CONSTRAINT FK_9CC43ED3A76ED395 FOREIGN KEY (user_id) REFERENCES tc_users (id)');
        $this->addSql('CREATE INDEX IDX_9CC43ED3A76ED395 ON tc_tasks (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tc_tasks DROP FOREIGN KEY FK_9CC43ED3A76ED395');
        $this->addSql('DROP INDEX IDX_9CC43ED3A76ED395 ON tc_tasks');
        $this->addSql('ALTER TABLE tc_tasks DROP user_id');
    }
}
