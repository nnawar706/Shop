<?php
require 'vendor/autoload.php';
$f3 = Base::instance();
$cron = Cron::instance();
$cron->web=TRUE;
$cron->set('Job1', 'CronController->job', '@daily');

$f3->config('config/config.ini');
$f3->config('config/database.ini');
$f3->config('config/routes.ini');
$f3->config('config/validation.ini');

$f3->run();