<?php

require_once __DIR__.'/../ryzom_extra.php';

//
$filter = [
	'race' => false, // falce || ['fy' => true, 'ma' => true]
	'gender' => false, // false || ['m' => true, 'f' => true]
	'min_level' => 0,
	'max_level' => 999,
];
//
if (isset($argv[1])) {
	$filter['race'] = [$argv[1] => true];
}
//

//$creature = ryzom_creature_info('micro_boss_yubo.creature');
//
$result = [];
$files = glob(RYZOM_EXTRA_SHEETS_CACHE.'/creature-*.serial');
foreach($files as $file) {
	$dataset = ryzom_extra_load_dataset($file);
	foreach(array_keys($dataset) as $k){
		$cr = ryzom_creature_info($k);
		if ($cr === false) {
			echo "ERR: $k not found\n";
		} else {
			$result[$cr['race']][] = $cr;
		}
	}
}

dump_creatures($result, $filter);
exit;

function dump_creatures($creatures, $filter) {
	foreach($creatures as $race => $raceArray){
		if ($filter['race'] !== false && !isset($filter['race'][$race])) {
			continue;
		}
		echo "+ {$race} - ".count($raceArray)." sheets\n";
		foreach($raceArray as $sheet){
			if ($filter['gender'] !== false && !isset($filter['gender'][$sheet['gender']])) {
				echo " ! gender filter:[{$filter['gender']}], sheet:[{$sheet['gender']}]\n";
				continue;
			}
			if ($filter['min_level'] > $sheet['level'] || $filter['max_level'] < $sheet['level']) {
				echo " ! level:[{$filter['min_level']}, {$filter['max_level']}], sheet:[{$sheet['level']}]\n";
				continue;
			}

			$translation = ryzom_translate($sheet['sheetid'].'.creature', 'en');
			if (strstr($translation, 'NotFound:')!==false) {
				$translation = '#'.$sheet['sheetid'].'.creature';
			}
			printf("  g: %d, race: % 2d, spd: % 2d, lvl: (%d, %d, % 3d), ".
				"sel: %d, talk: %d, att:%d, giv:%d, mount:%d, ".
				"r:%d, f:%s, sheet:%s, _t:%s\n",
				$sheet['gender'], $sheet['race'], $sheet['speed'], $sheet['region_force'], $sheet['force_level'], $sheet['level'],
				$sheet['selectable'], $sheet['talkable'], $sheet['attackable'], $sheet['givable'], $sheet['mountable'],
				$sheet['display_in_radar'], $sheet['fame'], $sheet['sheetid'], $translation
			);
		}
	}
}

