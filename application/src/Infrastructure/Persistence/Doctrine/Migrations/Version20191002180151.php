<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191002180151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tweet (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', hashtag_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tweet_id INT NOT NULL, user_name VARCHAR(100) NOT NULL, user_image VARCHAR(100) DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_3D660A3B1041E39B (tweet_id), INDEX IDX_3D660A3BFB34EF56 (hashtag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hashtag (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) DEFAULT NULL, last_tweet INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3BFB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tweet DROP FOREIGN KEY FK_3D660A3BFB34EF56');
        $this->addSql('DROP TABLE tweet');
        $this->addSql('DROP TABLE hashtag');
    }
}
