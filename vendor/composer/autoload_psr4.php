<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'think\\worker\\' => array($vendorDir . '/topthink/think-worker/src'),
    'think\\composer\\' => array($vendorDir . '/topthink/think-installer/src'),
    'think\\' => array($baseDir . '/thinkphp/library/think'),
    'Workerman\\' => array($vendorDir . '/workerman/workerman-for-win', $vendorDir . '/workerman/workerman'),
);
