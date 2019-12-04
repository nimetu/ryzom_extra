<?php

$config = array();

// languages to export
$config['languages'] = ['en', 'fr', 'de', 'ru', 'es'];

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


// <sheet>_words_<lang>.txt from client
$config['words'] = [
	'uxt',
	'skill',
	'faction',
	'place',
	'item',
	'creature',
	'sbrick',
	'sphrase',
	'title',
	'outpost'
];

return $config;
