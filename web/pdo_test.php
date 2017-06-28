<?php

$a_db_credentials = require __DIR__ . '/../app/config/db_credentials.php';
echo "<h1>Testing PDO</h1>";

try {
  $db = new \PDO('mysql:host=' . $a_db_credentials['host']
    . ';dbname=' . $a_db_credentials['dbname']
    . ';charset=' . $a_db_credentials['charset'],
    $a_db_credentials['user'], $a_db_credentials['password']);

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


  $c_sql = file_get_contents(__DIR__ . '/../app/config/schema.sql');

  echo "<pre>{$c_sql}</pre>";

  if (!empty($c_sql)) {
    $db->exec($c_sql);
  }//endif

  $c_sql = 'INSERT INTO `users`(`email`, `password`) VALUES ('
  . '\'' . getFakeEmail() . '\''
  . ', '
  . '\'' . rand(1, 100) .  '\''
  . ' )';

  $db->exec($c_sql);

  $c_sql = 'SELECT * FROM `users`;';
  $stmt = $db->query($c_sql);
  $a_rows = $stmt->fetchAll();

  foreach ($a_rows as $a_row) {
    echo $a_row['email'] . $a_row['password'] . '<br>';
  }


} catch(PDOException $e) {
  echo $e->getMessage();
}


function getFakeEmail() : string
{
  return 'user_' . rand(1, 1000) . '@email.com';
}
