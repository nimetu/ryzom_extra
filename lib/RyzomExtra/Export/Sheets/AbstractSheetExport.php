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

namespace RyzomExtra\Export\Sheets;

use Nel\Misc\SheetId;
use RyzomExtra\Export\EncoderInterface;
use RyzomExtra\Export\ExportInterface;
use Ryzom\Sheets\SheetsManager;

abstract class AbstractSheetExport implements ExportInterface {
	/** @var EncoderInterface */
	protected $encoder;

	/** @var string */
	protected $path;

	/** @var SheetsManager */
	protected $sheetsManager;

	/** @var SheetId */
	protected $sheetIds;

	function __construct(SheetId $sheetIds, SheetsManager $sheetsManager, $path, EncoderInterface $encoder) {
		$this->sheetsManager = $sheetsManager;
		$this->sheetIds = $sheetIds;
		$this->path = $path;
		$this->encoder = $encoder;
	}

	protected function _serializeInto(array $data, $name) {
		$ext = $this->encoder->name();
		$filename = "{$this->path}/{$name}.{$ext}";
		echo "+ saving $filename\n";
		file_put_contents($filename, $this->encoder->encode($data));
	}

	/**
	 * Return mpft index name
	 *
	 * @param int $index
	 * @return string
	 */
	protected function getMpftName($index){
		$mpftNames = array(
			'mpftMpL', 'mpftMpH', 'mpftMpP', 'mpftMpM', 'mpftMpG', 'mpftMpC', 'mpftMpGA',
			'mpftMpPE', 'mpftMpCA', 'mpftMpE', 'mpftMpEN', 'mpftMpPR', 'mpftMpCR', 'mpftMpRI',
			'mpftMpRE', 'mpftMpAT', 'mpftMpSU', 'mpftMpED', 'mpftMpBT', 'mpftMpPES', 'mpftMpSH',
			'mpftMpTK', 'mpftMpJH', 'mpftMpCF', 'mpftMpVE', 'mpftMpMF',
			'mpft'
		);
		return $mpftNames[$index];
	}

	/**
	 * Return array of mpft names for bitfield
	 * Return array like array(0 => 'mpftMpL', 2 => 'mpftMpP')
	 *
	 * @param int $bitfield
	 *
	 * @return array
	 */
	protected function getMpftMap($bitfield) {
		$result = array();

		// loop until last '1' bit
		$bit = 0;
		while((1 << $bit) <= $bitfield){
			if($bitfield & (1 << $bit)){
				$result[$bit] = $this->getMpftName($bit);
			}
			$bit++;
		}
		return $result;
	}

	/**
	 * Returns array of stat names for mpft group (blade, etc)
	 *
	 * @param int $index
	 *
	 * @return array
	 */
	protected function getMpftStats($index) {
		$all_stats = 'durability|lightness|sap_load|dmg|speed|range|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection'.
			'|desert_resistance|forest_resistance|jungle_resistance|lake_resistance|prime_roots_resistance'.
			'|acid_protection|cold_protection|rot_protection|fire_protection|shockwave_protection|poison_protection|electric_protection'.
			'|elemental_cast_speed|elemental_power|off_affliction_cast_speed|off_affliction_power|def_affliction_cast_speed|def_affliction_power|heal_cast_speed|heal_power';
		$statGroupMap = array();
		$statGroupMap[0] = 'durability|lightness|sap_load|dmg|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[1] = 'durability|lightness|sap_load|dmg|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[2] = 'durability|lightness|sap_load|dmg|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[3] = 'durability|lightness|sap_load|dmg|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[4] = 'durability|lightness|sap_load|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[5] = 'durability|lightness|sap_load|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[6] = 'durability|lightness|sap_load|speed|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[7] = 'durability|lightness|sap_load|dmg|speed|range|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[8] = 'durability|lightness|sap_load|dmg|speed|range|dodge_modifier|parry_modifier|adversary_dodge_modifier|adversary_parry_modifier';
		$statGroupMap[9] = 'durability|lightness|dmg|speed|range';
		$statGroupMap[10] = 'durability|lightness|speed|range';
		$statGroupMap[11] = 'durability|lightness|dmg|speed|range';
		$statGroupMap[12] = 'durability|lightness|dodge_modifier|parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection';
		$statGroupMap[13] = 'durability|lightness|dodge_modifier|parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection';
		$statGroupMap[14] = 'durability|lightness|dodge_modifier|parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection';
		$statGroupMap[15] = 'durability|lightness|dodge_modifier|parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection';
		$statGroupMap[16] = 'durability|lightness|desert_resistance|forest_resistance|jungle_resistance|lake_resistance|prime_roots_resistance';
		$statGroupMap[17] = 'durability|lightness|acid_protection|cold_protection|rot_protection|fire_protection|shockwave_protection|poison_protection|electric_protection';
		$statGroupMap[18] = $all_stats;
		$statGroupMap[19] = $all_stats;
		$statGroupMap[20] = $all_stats;
		$statGroupMap[21] = $all_stats;
		$statGroupMap[22] = $all_stats;
		$statGroupMap[23] = $all_stats;
		$statGroupMap[24] = 'durability|lightness|dodge_modifier|parry_modifier|protection_factor|max_slashing_protection|max_smashing_protection|max_piercing_protection';
		$statGroupMap[25] = 'durability|lightness|sap_load|elemental_cast_speed|elemental_power|off_affliction_cast_speed|off_affliction_power|def_affliction_cast_speed|def_affliction_power|heal_cast_speed|heal_power';

		return explode('|', $statGroupMap[$index]);
	}

	/**
	 * Return stat name index
	 *
	 * @param string $name stat name like 'durability', 'lightness', etc
	 *
	 * @return mixed
	 */
	protected function getStatIndex($name) {
		$statNames = array(
			'durability', 'lightness', 'sap_load', 'dmg', 'speed', 'range',
			'dodge_modifier', 'parry_modifier', 'adversary_dodge_modifier', 'adversary_parry_modifier',
			'protection_factor', 'max_slashing_protection', 'max_smashing_protection', 'max_piercing_protection',
			'acid_protection', 'cold_protection', 'rot_protection',
			'fire_protection', 'shockwave_protection', 'poison_protection', 'electric_protection',
			'desert_resistance', 'forest_resistance', 'lake_resistance', 'jungle_resistance', 'prime_roots_resistance',
			'elemental_cast_speed', 'elemental_power',
			'off_affliction_cast_speed', 'off_affliction_power',
			'def_affliction_cast_speed', 'def_affliction_power',
			'heal_cast_speed', 'heal_power',
		);
		return array_search($name, $statNames, true);
	}
}
