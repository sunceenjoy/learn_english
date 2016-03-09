#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/bootstrap.php';

$application = new Application();

// Migrations Commands
$configure = new \Doctrine\DBAL\Migrations\Configuration\Configuration($c['db.eng']);
$configure->setMigrationsNamespace('EngMigration');
$configure->setMigrationsDirectory(DOCROOT.'/database-migration/versions');
$configure->registerMigrationsFromDirectory($configure->getMigrationsDirectory());
$configure->setMigrationsTableName('english_migration_versions');
$symfony_output = new \Symfony\Component\Console\Output\ConsoleOutput();
$symfony_output->setDecorated(true);
$configure->setOutputWriter(new \Doctrine\DBAL\Migrations\OutputWriter(function ($msg) use ($symfony_output) {
    $symfony_output->writeln($msg);
}));
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($c['db.eng']),
    'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
    'configuration' => new \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper($c['db.eng'], $configure),
));
$application->setHelperSet($helperSet);

$application->addCommands(array(
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
));

$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\GenerateEntitiesDoctrineCommand());
$application->add(new \Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand());

$application->add(new \Eng\Core\Command\Util\VoiceManager($c));

$application->run();
