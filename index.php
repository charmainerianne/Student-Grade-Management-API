<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

use Charmaine\StudentGradeApi\Controllers\StudentController;

$controller = new StudentController();
echo $controller->checkStatus();
