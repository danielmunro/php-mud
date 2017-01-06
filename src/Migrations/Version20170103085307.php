<?php

namespace PhpMud\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170103085307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Mob ADD startRoom_id INT DEFAULT NULL;
ALTER TABLE Mob ADD CONSTRAINT FK_C6DAB09DA856391C FOREIGN KEY (startRoom_id) REFERENCES Room (id);
CREATE INDEX IDX_C6DAB09DA856391C ON Mob (startRoom_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
