<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/5/14
 * Time: 3:02 PM
 */



if (is_readable(realpath(__DIR__ . '/composer.lock'))) {
    require_once realpath(__DIR__ . '/vendor/autoload.php');
}
else {
    throw new \Exception('Install the project first, use: composer install --dev');
}


