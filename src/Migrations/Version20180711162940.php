<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711162940 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers ADD one_who_answered_id INT NOT NULL, ADD creatorname VARCHAR(100) NOT NULL, CHANGE correctanswer correctanswer TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE questions ADD authorname VARCHAR(100) NOT NULL, ADD image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answers DROP one_who_answered_id, DROP creatorname, CHANGE correctanswer correctanswer TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE questions DROP authorname, DROP image');
    }
}
