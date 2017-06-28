<?php
// development environment configuration

require __DIR__ . '/common.php';

use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\MonologServiceProvider;


// enable the debug mode
$app['debug'] = true;

$app->register(new WebProfilerServiceProvider(), [
  'profiler.cache_dir' => __DIR__ . '/../cache/profiler'
]);


// как я понял, это создает в объекте $app ключ $app['monolog']
$app->register(new MonologServiceProvider(), [
  'monolog.logfile' => __DIR__ . '/../logs/silex_dev.log',
  'monolog.level' => 'debug'
]);

//echo isset($app['monolog'])? "Ключ 'monolog' существует" : "Ключ 'monolog' не существует";
