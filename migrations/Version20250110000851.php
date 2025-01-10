<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110000851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_books (user_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_A8D9D1CAA76ED395 (user_id), INDEX IDX_A8D9D1CA16A2B381 (book_id), PRIMARY KEY(user_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_books ADD CONSTRAINT FK_A8D9D1CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_books ADD CONSTRAINT FK_A8D9D1CA16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A76ED395');
        $this->addSql('DROP INDEX IDX_CBE5A331A76ED395 ON book');
        $this->addSql('ALTER TABLE book DROP user_id');
        $this->addSql('ALTER TABLE user DROP roles, DROP password');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_books DROP FOREIGN KEY FK_A8D9D1CAA76ED395');
        $this->addSql('ALTER TABLE user_books DROP FOREIGN KEY FK_A8D9D1CA16A2B381');
        $this->addSql('DROP TABLE user_books');
        $this->addSql('ALTER TABLE book ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CBE5A331A76ED395 ON book (user_id)');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, ADD password VARCHAR(255) NOT NULL');
    }
}
