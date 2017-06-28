<?php
require_once 'vendor/autoload.php';

use Doctrine\ORM\ {
  Tools\Console\Helper\EntityManagerHelper,
  Tools\Console\Command
};

use Doctrine\DBAL\ {
  Tools\Console\Helper\ConnectionHelper,
  Connection,
  Version
};

use Symfony\Component\Console\ {
  Application as CliApplication,
  Helper\HelperSet
};



// здесь настройки Doctrine
$app = require 'app/app.php';
// а именно, в ключе 'em' мы сохранили созданный EntityManager
$em = $app['em'];

// как я понимаю, в примере ниже мы пользуемся консолью из Symfony,
// но в документации Doctrine - они предлагают свой класс
// \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);

$cli = new CliApplication('Doctrine Command line Interface', Version::VERSION);
$cli->setCatchExceptions(true);

$cli->setHelperSet(new HelperSet([
  'db' => new ConnectionHelper($em->getConnection()),
  'em' => new EntityManagerHelper($em)
]));

$cli->addCommands([
  new Command\GenerateRepositoriesCommand,
  new Command\GenerateEntitiesCommand,
  new Command\ConvertMappingCommand,
  new Command\ValidateSchemaCommand,
  new Command\SchemaTool\CreateCommand,
  new Command\SchemaTool\UpdateCommand,
  new Command\GenerateProxiesCommand
]);

$cli->run();
