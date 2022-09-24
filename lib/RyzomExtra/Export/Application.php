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

namespace RyzomExtra\Export;

use Pimple;
use Nel\Misc\SheetId;
use Nel\Misc\BnpFile;
use Ryzom\Sheets\PackedSheetsLoader;
use Ryzom\Sheets\SheetsManager;
use Ryzom\Sheets\VisualSlotManager;
use Ryzom\Translation\Loader\WordsLoader;
use Ryzom\Translation\Loader\UxtLoader;
use RyzomExtra\Export\Encoder\SerializeEncoder;

class Application extends Pimple {

	function __construct() {
		$app = $this;

		// leveldesign.bnp has 'sheet_id.bin' */
		$this['bnp.leveldesign'] = $app->share(function() use ($app) {
			return new BnpFile($app['data.path'].'/leveldesign.bnp');
		});

		// gamedev.bnp has translations (<sheet>_words_<lang>.txt, <lang>.uxt)
		$this['bnp.gamedev'] = $app->share(function() use ($app) {
			return new BnpFile($app['data.path'].'/gamedev.bnp');
		});

		$this['bnp.data_common'] = $app->share(function () use ($app) {
			return new BnpFile($app['data.path'].'/data_common.bnp');
		});

		// SheetIds collection, sheet_id.bin reader
		$this['sheetid'] = $app->share(function () use ($app) {
			$sheetIds = new SheetId();

			$file = 'sheet_id.bin';

			/** @var BnpFile $bnp */
			$bnp = $app['bnp.leveldesign'];
			if ($bnp->hasFile($file)) {
				$app->debug('loading %s', $file);
				$data = $bnp->readFile($file);

				$sheetIds->load($data);
			}
			return $sheetIds;
		});

		// packed sheets collection
		// - depends on SheetId and PackedSheetsLoader
		$this['sheets'] = $this->share(function() use ($app) {
			return new SheetsManager($app['sheetid'], $app['load.packed_sheets']);
		});

		// Packed sheets loader
		$this['load.packed_sheets'] = $this->share(function() use ($app) {
			return new PackedSheetsLoader($app['data.path']);
		});

		// <sheet>_words_<lang>.txt reader
		$this['load.words'] = $this->share(function() use ($app) {
			return new WordsLoader();
		});

		// Reader for <lang>.uxt files
		$this['load.uxt'] = $this->share(function() use ($app) {
			return new UxtLoader();
		});

		// Serialize encoder to use
		$this['encoder'] = $this->share(function() {
			return new SerializeEncoder();
		});

		// Save SheetId collection into cache files
		$this['export.sheetid'] = $this->share(function() use ($app) {
			return new SheetIdExport($app['sheetid'], $app['cache.path'], $app['encoder']);
		});

		// Save words and uxt translations into cache files
		$this['export.words'] = $this->share(function() use ($app) {
			return new WordsExport($app['cache.path'], $app['encoder']);
		});

		// Save loaded sheets to cache files
		$this['export.packed_sheets'] = $this->share(function() use ($app) {
			return new PackedSheetsExport($app['sheetid'], $app['sheets'], $app['cache.path'], $app['encoder']);
		});

		// Save visual_slot.tab to cache file
		$this['export.visual_slot'] = $this->share(function () use ($app) {
			return new VisualSlotExport($app['sheetid'], $app['cache.path'], $app['encoder']);
		});
	}

	function exportSheetIds() {
		$array = $this['sheetid']->getSheets();

		$this->debug('>> sheet_id.bin has %d entries', count($array));
		$this['export.sheetid']->export($array, 'sheets');
	}

	function exportSheets($sheetKeys) {
		/** @var SheetsManager $sheets */
		$sheets = $this['sheets'];

		/** @var PackedSheetsExport $export */
		$export = $this['export.packed_sheets'];

		foreach ($sheetKeys as $sheet) {
			$this->debug('loading %s', $sheet);
			$sheets->load($sheet);
		}

		foreach ($sheets->getLoadedSheets() as $sheet) {
			$ps = $sheets->load($sheet);
			if (!empty($ps)) {
				$array = $ps->getSheets();
				$this->debug('exporting %s, %d items', $sheet, count($array));
				$export->export($array, $sheet);
			}
		}
	}

	function exportTranslations($lang, $sheets) {
		if (!is_array($sheets)) {
			$sheets = array($sheets);
		}
		$this->debug('loading translations (%s) (%s)', $lang, join(', ', $sheets));

		/** @var BnpFile $bnp */
		$bnp = $this['bnp.gamedev'];
		foreach ($sheets as $sheet) {
			if ($sheet == 'uxt') {
				$name = sprintf('%s.uxt', $lang);
				$loader = 'load.uxt';
			} else {
				$name = sprintf('%s_words_%s.txt', $sheet, $lang);
				$loader = 'load.words';
			}

			if (!$bnp->hasFile($name)) {
				$this->debug('file %s not found', $name);
				continue;
			}

			$data = $bnp->readFile($name);
			$array = $this[$loader]->load($sheet, $data);
			// for sbrick, replace placeholders with real values
			if (!empty($array['sbrick'])) {
				$array['sbrick'] = $this->fixStubDescription($array['sbrick'], 'sbrick');
			}
			$this['export.words']->export($array, $lang);
		}
	}

	/**
	 * Export from files.
	 *
	 * @param array $sheetLangFiles sheet/lang/file array like
	 *                              array(
	 *                                'title' => array(
	 *                                  'en' => 'title_words_en.txt'
	 *                                  'fr' => 'title_words_fr.txt'
	 *                                )
	 *                              )
	 */
	function exportTranslationFromFiles($sheetLangFiles) {
		$this->debug('loading translations for sheets (%s)', join(', ', array_keys($sheetLangFiles)));
		foreach ($sheetLangFiles as $sheet => $langFiles) {
			if ($sheet == 'ext') {
				$loader = 'load.uxt';
			} else {
				$loader = 'load.words';
			}

			foreach($langFiles as $lang => $file) {
				if (!file_exists($file)) {
					$this->debug('sheet(%s), lang(%s), file(%s) not found', $sheet, $lang, $file);
					continue;
				}
				$data = file_get_contents($file);
				$array = $this[$loader]->load($sheet, $data);
				$this['export.words']->export($array, $lang);
			}
		}
	}

	function exportVisualSlots() {
		$this->debug('loading visual_slot.tab');

		$buf = file_get_contents($this['data.path'].'/visual_slot.tab');

		$vs = new VisualSlotManager();
		$vs->load($buf);
		$vsMap = $vs->getIndexMap();

		$this['export.visual_slot']->export($vsMap, 'visual_slot');
	}

	function fixStubDescription(array $messages, $prefix) {
		/** @var SheetId $sheetIds */
		$sheetIds = $this['sheetid'];

		/** @var SheetsManager $sheetsManager */
		$sheetsManager = $this['sheets'];

		foreach ($messages as $key => &$row) {
			$sheetId = $sheetIds->getSheetId($key.'.'.$prefix);
			if($sheetId === null){
				printf("- numeric sheetid not found (%s)\n", $key.'.'.$prefix);
				continue;
			}
			/** @var \Ryzom\Sheets\Client\SbrickSheet $sheet */
			$sheet = $sheetsManager->findById($sheetId);
			if (!empty($sheet) && !empty($sheet->Properties)) {
				// Properties is array of strings like 'SP_SHIELDING:25:5:50:10:75:15:15:120'
				$props = array();
				foreach ($sheet->Properties as $prop) {
					$pairs = explode(':', $prop->Text);
					$pairs[0] = trim($pairs[0]);

					$propKey = strtolower($pairs[0]);
					$props[$propKey] = array_slice($pairs, 1);
				}

				foreach (array('name', 'description', 'description2', 'tooltip') as $msgIndex) {
					// description is like
					// Shielding ($6SP_SHIELDING s/$7SP_SHIELDING s) - Shield ($4SP_SHIELDING%/$5SP_SHIELDING)
					if (isset($row[$msgIndex])
						&& preg_match_all('/(?:\$(?:\|)?(\d{0,})(\w+))/', $row[$msgIndex], $match)
					) {
						foreach($match[0] as $k => $stub){
							$propKey = strtolower($match[2][$k]);
							$propIdx = !empty($match[1][$k]) ? $match[1][$k] : 0;
							if(isset($props[$propKey]) && isset($props[$propKey][$propIdx])){
								$propVal = trim($props[$propKey][$propIdx]);
								$row[$msgIndex] = str_ireplace($stub, $propVal, $row[$msgIndex]);
							}
						}
					}
				}
			}
		}
		unset($row);
		return $messages;
	}

	function debug($fmt) {
		$fmt = date('H:i:s').' '.$fmt.PHP_EOL;
		$args = array_slice(func_get_args(), 1);
		if (!empty($args)) {
			vprintf($fmt, $args);
		} else {
			echo $fmt;
		}
	}
}

