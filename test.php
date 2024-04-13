<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

use FpDbTest\Database;
use FpDbTest\DatabaseTest;

$mysqli = require_once __DIR__ . DIRECTORY_SEPARATOR . "mysqli.php";
if ($mysqli->connect_errno) {
    throw new Exception($mysqli->connect_error);
}

$db = new Database($mysqli);
$test = new DatabaseTest($db);
$test->testBuildQuery();

exit('OK');
