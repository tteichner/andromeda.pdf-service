<?php
// extended time limit settings and some other environment configs
ini_set('max_execution_time', 14400);
ini_set('pcre.backtrack_limit', -1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

// system constant definitions
const FAA_PATHS_ROOT_ABS = '/var/www/storage';
const FAA_MASTER_PASSWORD = '23c8d367124f1ef55f07624790e7809e';
const FAA_CERT_VALIDATION_ACTIVE = false;

// import all the handlers and objects
require_once 'vendor/autoload.php';
require_once 'objects/request.php';
require_once 'handler/pdf.php';
require_once 'service/functions.php';

$src = __DIR__ . '/requests.json';
$psrc = __DIR__ . '/requests.php';
if (!is_file($psrc) || filemtime($src) > filemtime($psrc)) {
    $map = json_decode(file_get_contents($src));
    $data = var_export($map, true);
    file_put_contents($psrc, '<?php $map = ' . $data . ';');
} else {
    require_once $psrc;
}
