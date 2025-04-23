<?php
/* Paths */
define('HOME_PATH', __DIR__);                     //  C:\xampp\htdocs\pdf-tool
define('BASE_URL' , 'http://localhost/pdf-tool/');
define('FILES_PATH' , HOME_PATH . '/uploads');
define('OUTPUT_PATH', HOME_PATH . '/output');

/* Create folders if missing */
foreach ([FILES_PATH, OUTPUT_PATH] as $d) {
    if (!is_dir($d))  mkdir($d, 0755, true);
    if (!is_writable($d)) die("Folder $d not writable");
}

/* Limits */
define('MAX_FILE_SIZE', 10 * 1024 * 1024);        // 10 MB
define('ALLOWED_FILE_TYPES', ['application/pdf']);
