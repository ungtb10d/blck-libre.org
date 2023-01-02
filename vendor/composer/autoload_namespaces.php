<?php

// autoload_namespaces.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'phpDocumentor' => array($vendorDir . '/phpdocumentor/reflection-docblock/src'),
    'ValueValidators\\' => array($vendorDir . '/data-values/interfaces/src'),
    'ValueParsers\\' => array($vendorDir . '/data-values/interfaces/src', $vendorDir . '/data-values/common/src'),
    'ValueFormatters\\' => array($vendorDir . '/data-values/interfaces/src', $vendorDir . '/data-values/common/src'),
    'Psr\\Log\\' => array($vendorDir . '/psr/log'),
    'Prophecy\\' => array($vendorDir . '/phpspec/prophecy/src'),
    'Liuggio' => array($vendorDir . '/liuggio/statsd-php-client/src'),
    'JsonSchema' => array($vendorDir . '/justinrainbow/json-schema/src'),
    'DataValues\\' => array($vendorDir . '/data-values/data-values/src', $vendorDir . '/data-values/common/src'),
    'Composer\\Installers\\' => array($vendorDir . '/composer/installers/src'),
    'ComposerHookHandler' => array($baseDir . '/includes/composer'),
    '' => array($vendorDir . '/cssjanus/cssjanus/src'),
);
