<?php
// Похоже, это не работает
$app['twig.path'] = array(
    __DIR__ . '/../../templates',
    __DIR__ . '/../../web/js'
);

$app['twig.options'] = array('cache' => __DIR__ . '/../cache/twig');

$app['image_path'] = '/images';
$app['upload_dir'] = __DIR__ . '/../../web' . $app['image_path'];
