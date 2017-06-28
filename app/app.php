<?php
// the created here $app variable must be returned to index_dev.php

use Silex\Application as SilexApplication;
use Silex\Provider\ {
  TwigServiceProvider,
  ServiceControllerServiceProvider,
  HttpFragmentServiceProvider,
  // UrlGeneratorServiceProvider, см. коммент ниже
  RoutingServiceProvider,
  DoctrineServiceProvider,
  ValidatorServiceProvider
  };

use Doctrine\ORM\ {
  Tools\Setup as DoctrineSetup,
  EntityManager as DoctrineEntityManager
};

$app = new SilexApplication();

$app->register(new ServiceControllerServiceProvider());

$app->register(new TwigServiceProvider()
  //, ['twig.path' => __DIR__ . '/../templates']
);

$app->register(new HttpFragmentServiceProvider());

// UrlGeneratorServiceProvider disapeared in version 2.0 ?
// It looks like all routing-related stuff has been moved into RoutingServiceProvider
$app->register(new RoutingServiceProvider());

$app->register(new ValidatorServiceProvider());

// NB! Этот массив также используется ниже для DoctrineEntityManager
// Может это масло-маляное? и от одного определения можно избавиться,
// Эта закомментированная строка никак не повлияла на работоспособность.
$a_db_credentials = require 'config/db_credentials.php';
$app->register(new DoctrineServiceProvider(), [
//  'db.options' => $a_db_credentials
]);


// Не понимаю, что делает этот кусок кода
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...
    $twig->addFunction(new \Twig_Function('asset', function ($asset) use ($app) { // version 1.x \Twig_SimpleFunction
        return $app['request_stack']->getMasterRequest()->getBasepath() . '/' . $asset;
    }));
    return $twig;
});



// Doctrine configuration
//
// Creates a configuration with a yaml metadata driver.
// Get an instance of the ORM Configuration object using the Setup helper.
// @param array   $paths
// @param boolean $isDevMode
// @param string  $proxyDir
// @param Cache   $cache
// @return Configuration
// Оказывается, конфигурацию можно также задать в других форматах:
// configuration for Annotations, XML, or YAML
// см. http://docs.doctrine-project.org/en/latest/tutorials/getting-started.html#obtaining-the-entitymanager
$config = DoctrineSetup::createYAMLMetadataConfiguration(
  $paths = array(__DIR__ . "/../src/App/Model"),
  $isDevMode = false,
  $proxyDir = __DIR__ . '/proxies/');

$config->setAutoGenerateProxyClasses(true);

// obtaining the entity manager
// the EntityManager is obtained from a factory method
// Нужно передать массив параметров конфигурации базы данных.
// Важным является ключ 'driver' => 'pdo_mysql', который определяет остальные
// Про опции читать:
// http://www.doctrine-project.org/documentation/manual/2_0/en/dbal
$app['em'] = DoctrineEntityManager::create($a_db_credentials, $config);



// Inject a global variable with a csrf manager class into the twig template engine
$app->before(function() use ($app) {
  $app['twig']->addGlobal('csrf', $app['csrf_manager']);
});

return $app;
