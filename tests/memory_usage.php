<?php

require_once __DIR__.'/../ryzom_extra.php';

function load_datasets(array $array) {
	static $cache = [];
	echo "+ ";
	$_mem = memory_get_peak_usage();
	$_time = microtime(true);
	foreach ($array as $ds) {
		echo "{$ds} ";
		$cache[$ds] = ryzom_extra_load_dataset(RYZOM_EXTRA_SHEETS_CACHE.'/'.$ds.'.serial');
	}
	echo "\n";
	printf("% 15s bytes\n", number_format(memory_get_peak_usage() - $_mem, 2, '.', ','));
}


echo "//\n// dataset memory usage\n//\n";
load_datasets([
	'words_en_creature',
	'words_en_faction',
	'words_en_item',
	'words_en_outpost',
	'words_en_outpost_building',
	'words_en_outpost_squad',
	'words_en_place',
	'words_en_sbrick',
	'words_en_skill',
	'words_en_sphrase',
	'words_en_title',
	'words_en_uxt',
]);
load_datasets(['craftplan']);
load_datasets(['items']);
load_datasets(['resource_stats']);
load_datasets(['sbrick']);
load_datasets([
	'sheets-00',
	'sheets-01',
	'sheets-02',
	'sheets-03',
	'sheets-04',
	'sheets-05',
	'sheets-06',
	'sheets-07',
	'sheets-08',
	'sheets-09',
]);
load_datasets(['skilltree']);
load_datasets(['visual_slot']);

printf("% 15s bytes\n", number_format(memory_get_peak_usage(), 2, '.', ','));

