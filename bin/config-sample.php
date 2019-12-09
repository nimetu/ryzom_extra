<?php

$config = array();

// languages to export
$config['languages'] = ['en', 'fr', 'de', 'ru', 'es'];

// Ryzom client data directory location with .packed_sheets and .bnp files
// - ./              (packed_sheets, visual_slot.tab)
// - leveldesign.bnp (sheet_id.bin)
// - gamedev.bnp     (words, uxt translations)
$config['data.path'] = '/srv/ryzom-client/data';

// path to directory where ryzom-data translated files are (ie, race_words_en.txt)
// these files are from https://gitlab.com/ryzom/ryzom-data repository.
// if left empty (default), then this is ignored
$config['words_extra.path'] = '';// <path-to-ryzom-data>/translations/translated';

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

// <sheet>_words_<lang>.txt from ryzom-data
$config['words_extra'] = [
	'characteristic',
	'damagetype',
	'ecosystem',
	'race',
	'score',
];

return $config;
