<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130112459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE article_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT article_tag_pkey');
        $this->addSql('ALTER TABLE article_tag ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE article_tag ALTER article_id DROP NOT NULL');
        $this->addSql('ALTER TABLE article_tag ALTER tag_id DROP NOT NULL');
        $this->addSql('ALTER TABLE article_tag ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE article_tag_id_seq CASCADE');
        $this->addSql('DROP INDEX article_tag_pkey');
        $this->addSql('ALTER TABLE article_tag DROP id');
        $this->addSql('ALTER TABLE article_tag ALTER article_id SET NOT NULL');
        $this->addSql('ALTER TABLE article_tag ALTER tag_id SET NOT NULL');
        $this->addSql('ALTER TABLE article_tag ADD PRIMARY KEY (article_id, tag_id)');
    }
}
