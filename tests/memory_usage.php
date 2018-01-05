<?php

ini_set('memory_limit', '-1');

require_once __DIR__.'/../ryzom_extra.php';

function load_datasets(array $array) {
	static $cache = [];
	echo "+ ";
	$_mem = memory_get_peak_usage();
	$_time = microtime(true);
	foreach ($array as $ds) {
		$fname = RYZOM_EXTRA_SHEETS_CACHE.'/'.$ds.'.serial';
		if (file_exists($fname)) {
			echo "{$ds} ";
			$cache[$ds] = ryzom_extra_load_dataset($fname);
		} else {
			echo "!{$dst}";
		}
	}
	echo "\n";
	printf("% 15s bytes\n", number_format(memory_get_peak_usage() - $_mem, 2, '.', ','));
}

function group_datasets($dir) {
	$files = glob($dir);

	$groups = array();
	foreach($files as $file) {
		$fname = basename($file);
		$fname = substr(basename($file), 0, strrpos($fname, '.'));
		if (strstr($fname, 'words_')) {
			$group = substr($fname, 0, strlen('words_??'));
		} elseif (strstr($fname, '-') !== false) {
			$group = substr($fname, 0, strpos($fname, '-'));
		} else {
			$group = $fname;
		}
		$groups[$group][] = $fname;
	}
	return $groups;
}

echo "//\n// dataset memory usage\n//\n";

$groups = group_datasets(RYZOM_EXTRA_SHEETS_CACHE.'/*.serial');
foreach($groups as $group => $files) {
	load_datasets($files);
}

printf("% 15s bytes\n", number_format(memory_get_peak_usage(), 2, '.', ','));

