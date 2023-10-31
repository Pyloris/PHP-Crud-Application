<?php

require_once __DIR__ . "/vendor/autoload.php";

// components needed from the framework
use sirJuni\Framework\Application\Application;

// include all the application files needed.
require_once __DIR__ . "/App/routes.php";


$app = new Application();

// give request to framework for handling
$app->handle();
?>