<?php

require_once __DIR__.'/../vendor/autoload.php';

$tick = file_get_contents('http://api.ryzom.com/time.php?format=raw');
$text = file_get_contents('http://api.ryzom.com/time.php?format=txt');

$date = new \RyzomExtra\AtysDateTime();
$date->setGameCycle($tick);

$got = $date->formatDate(true);
if ($text == $got) {
	echo "PASS :: Both dates are the same ({$text})\n";
} else {
	echo "FAIL :: Dates are differentt:\nExpected: ({$text})\nGot     : ({$got})\n";
}

