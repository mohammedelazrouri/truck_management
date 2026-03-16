<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Controllers/ManagePointsGoodsController.php';
require_once __DIR__ . '/Models/Point.php';
require_once __DIR__ . '/Models/Product.php';

$controller = new \Admin\Controllers\ManagePointsGoodsController($pdo);
$controller->handle();
