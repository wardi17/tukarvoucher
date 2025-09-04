<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://";

$host = $_SERVER['HTTP_HOST'];
//$currentUrl = $protocol . $host;
$currentUrl = $protocol . $host.'/wardibaset';
 define('base_url', $currentUrl.'/voucher/public');
//define('base_url',"http://192.168.11.186/live_streaming/");
 //define('base_url', $currentUrl.'/kampanye_teraden/public');
define('FOLDER', '../public/uploads_attachfile/');

define('DB_HOST', 'localhost');
define('DB_USER', 'sa');
define('DB_PASS', '');
define('DB_NAME', 'um_db');
define('DB_NAME2','crm-bmi');
//define('SERVER_DB', '(LOCAL)');
define('SERVER_DB', 'DESKTOP-1CEB0AJ\SQLEXPRESS');
define('SESSION_TIMEOUT', 1800);