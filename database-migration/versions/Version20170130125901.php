<?php

namespace EngMigration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170130125901 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = <<<SQL
          CREATE TABLE IF NOT EXISTS `users` (
        `id` bigint(20) NOT NULL,
        `username` varchar(100) COLLATE utf8_bin NOT NULL,
        `password` char(100) COLLATE utf8_bin NOT NULL,
        `roles` varchar(100) COLLATE utf8_bin NOT NULL,
        `gender` tinyint(4) NOT NULL,
        `birthday` date NOT NULL,
        `nickname` varchar(100) COLLATE utf8_bin NOT NULL,
        `email` varchar(100) COLLATE utf8_bin NOT NULL,
        `website` varchar(100) COLLATE utf8_bin NOT NULL,
        `last_login_date` datetime DEFAULT NULL,
        `status` int(11) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;  
      ALTER TABLE `users`  ADD PRIMARY KEY (`id`);
      ALTER TABLE `users`  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
SQL;
        $this->addSql($sql);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $sql = <<<SQL
                DROP TABLE IF EXISTS `users`;
SQL;
    }
}
