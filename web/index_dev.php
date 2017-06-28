<?php
use Symfony\Component\Debug\Debug;

//exit("Here we are = index_dev.php");
require_once __DIR__ . '/../vendor/autoload.php';

Debug::enable();

$app = require __DIR__ . '/../app/app.php';

// здесь происходит несколько вызовов $app->register с всякими ...ServiceProvider
require __DIR__ . '/../app/config/dev.php';


// Вот самый важный файл:
// Здесь происходит добавление новых "индексов" в $app вроде $app['csrf_manager']
// и указание URL для разрешения адреса запроса
require __DIR__ . '/../app/config/routing.php';

$app->run();
