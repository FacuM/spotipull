<?php

require '../../vendor/autoload.php';

require '../include/functions.php';

$app = new \Slim\App;

require '../include/routes.php';

$app->run();

?>