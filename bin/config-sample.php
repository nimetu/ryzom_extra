<?php

$config = array();

// Ryzom client data directory location
// with .packed_sheets and .bnp files
$config['data.path'] = '/srv/ryzom-client/data';

// RyzomExtra directory for .serial files
$config['cache.path'] = __DIR__.'/../resources/sheets-cache';

// output format
$config['encoder'] = 'serial';
$config['sheets'] = [
	'item',
	'sitem', 'skill_tree', 'sbrick', 'sphrase',
	'creature',
	//'outpost', 'outpost_squad', 'outpost_building',
	//'faction',
];

return $config;
