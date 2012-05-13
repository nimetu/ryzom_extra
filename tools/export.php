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
require_once __DIR__.'/vendor/symfony/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->register();

$loader->registerNamespaces(array(
	'Nel' => __DIR__.'/vendor/ryzom_sheets/src',
	'Ryzom' => __DIR__.'/vendor/ryzom_sheets/src',
	'RyzomExtra' => __DIR__.'/src',
	'Symfony' => __DIR__.'/vendor/symfony',
));

$loader->registerPrefixes(array(
	'Pimple' => __DIR__.'/vendor/pimple',
));

/***/
if (!file_exists(__DIR__.'/config.php')) {
	die("- unable to find config.php");
}
$config = require_once __DIR__.'/config.php';

/***/
$app = new \RyzomExtra\Application();
$app['data.path'] = $config['data.path'];
$app['cache.path'] = $config['cache.path'];

// sheet_id.bin
$app->exportSheetIds();

// .packed_sheets
$app->exportSheets(array(
	//'item',
	'sitem', 'skill_tree', 'sbrick', 'sphrase',
	//'creature',
	//'outpost', 'outpost_squad', 'outpost_building',
	//'faction',
));

// <sheet>_words_<lang>.txt
$langArray = array('en', 'fr', 'de', 'ru', 'es');
$sheets = array('uxt', 'skill', 'faction', 'place', 'item', 'creature', 'sbrick', 'sphrase', 'title', 'outpost');
foreach ($langArray as $lang) {
	$app->exportTranslations($lang, $sheets);
}

printf("+ DEBUG: memory used %s bytes\n", number_format(memory_get_usage(true) - $mem));

