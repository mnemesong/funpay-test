<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "autoload.php";

\FpDbTest\QueryProcessorTest::initDefault()->runAll();

exit("OK");