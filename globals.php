<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8'); //cannot be usd becasue it isnot sutablefor form submission
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/DB.php';
require_once __DIR__.'/functions.php';
$loader = new \Twig\Loader\FilesystemLoader([__DIR__.'/templates', __DIR__.'/admin/admintemplates']);
$twig = new \Twig\Environment($loader, ['debug' => true, 'strict_variables'=> true]);
$ERROR  = '';
