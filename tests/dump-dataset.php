<?php

require_once __DIR__.'/../ryzom_extra.php';


if (!isset($argv[1])) {
	echo "{$argv[0]} <dataset>\n";
	exit;
}
$dataset = $argv[1];
if (substr($dataset, -7) !== '.serial') {
	$dataset .= '.serial';
}

$file = RYZOM_EXTRA_SHEETS_CACHE . '/' . $dataset;
$data = ryzom_extra_load_dataset($file);
print_r($data);

