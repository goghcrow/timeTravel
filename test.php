<?php
error_reporting(E_ALL);

define("EOL", php_sapi_name() === "cli" ? PHP_EOL : "<br>");
define("TRAVEL_REQ_NAME", "__time_travel__");

$travelFunc = require __DIR__ . "/timeTravel.php";
$travel = $travelFunc(TRAVEL_REQ_NAME);

echo "server time: " . date("Y-m-d H:i:s") . EOL;
if($travel()) {
	echo "travel to tim: " . date("Y-m-d H:i:s") . EOL;
} else {
	echo "travel fail" . EOL;
}
