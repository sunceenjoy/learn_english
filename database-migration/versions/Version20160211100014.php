<?php

namespace EngMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Delete useless table
 */
class Version20160211100014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("DROP TABLE Yellow");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `Yellow` (
  `id` int(11) NOT NULL,
  `url` char(100) NOT NULL,
  `img` char(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=776 DEFAULT CHARSET=utf8;
SQL;
        $this->addSql($sql);
    }
}
