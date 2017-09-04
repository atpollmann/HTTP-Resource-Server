<?php
define("BASEPATH", "/var/www/resource_server/");
include BASEPATH . "vendor/autoload.php";
\cl\pcorp\ResourceServer\app\FrontController::dispatch(BASEPATH . "config/config.json");