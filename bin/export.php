#!/usr/bin/php
<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2012 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomExtra.
//
// RyzomExtra is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomExtra is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

if (PHP_SAPI !== 'cli') {
	die('This script must be runned from command-line');
}

$mem = memory_get_usage(true);

/***/
require_once __DIR__.'/../vendor/autoload.php';

/***/
$opts = getopt("", array("config:"));
if (empty($opts['config'])) {
	$opts['config'] = __DIR__.'/config.php';
}
if (!file_exists($opts['config'])) {
	die("- unable to find {$opts['config']}".PHP_EOL);
}
$config = require_once $opts['config'];

/***/
$app = new \RyzomExtra\Export\Application();
$app['data.path'] = $config['data.path'];
$app['cache.path'] = $config['cache.path'];

if ($config['encoder'] == 'json') {
	$app['encoder'] = function(){
		return new \RyzomExtra\Export\Encoder\JsonEncoder();
	};
}

// sheet_id.bin
$app->exportSheetIds();

// .packed_sheets
$app->exportSheets($config['sheets']);

// visual_slot.tab
$app->exportVisualSlots();

// <sheet>_words_<lang>.txt
foreach ($config['languages'] as $lang) {
	$app->exportTranslations($lang, $config['words']);
}

// export translations from https://gitlab.com/ryzom/ryzom-data
if (!empty($config['words_extra.path'])) {
	$sheetLangFiles = [];
	foreach($config['languages'] as $lang) {
		foreach($config['words_extra'] as $sheet) {
			$sheetLangFiles[$sheet][$lang] = $config['words_extra.path']."/${sheet}_words_${lang}.txt";
		}
	}

	$app->exportTranslationFromFiles($sheetLangFiles);
}

printf("+ DEBUG: memory used %s bytes\n", number_format(memory_get_usage(true) - $mem));

