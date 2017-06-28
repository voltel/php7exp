<?php
// production environment configuration

require __DIR__ . '/common.php';

use Silex\Provider\MonologServiceProvider;

// disable the debug mode

$app->register(new MonologServiceProvider(), array(
  'monolog.logfile' => __DIR__ . '/../logs/silex_prod.log',
  'monolog.level' => 'error'
));
