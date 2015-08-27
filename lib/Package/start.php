<?php

if (! class_exists('\Package\Package')) {
    require  __DIR__ . '/Package.php';
}


$package = new \Package\Package( __DIR__ );

$package->run();

return $package;
