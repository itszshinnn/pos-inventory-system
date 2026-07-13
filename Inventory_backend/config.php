<?php

$config = [
    'SMTP_USER' => 'seanpaulforonda@gmail.com',
    'SMTP_PASS' => 'placeholder_app_password',
    'PAYMONGO_SECRET' => 'sk_test_placeholder_key',
];

$localConfigPath = __DIR__ . '/../config_local.php';

if (file_exists($localConfigPath)) {
    $localConfig = require $localConfigPath;
    $config = array_merge($config, $localConfig);
}

define('SMTP_USER', $config['SMTP_USER']);
define('SMTP_PASS', $config['SMTP_PASS']);
define('PAYMONGO_SECRET_KEY', $config['PAYMONGO_SECRET']);