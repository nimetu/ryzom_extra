<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2018 Meelis Mägi <nimetu@gmail.com>
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

/**
 * Export CharacterSheet to array using only few needed fields
 *
 * Creates file 'creature.serial'
 */
class CharacterSheetExport extends AbstractSheetExport {

	/**
	 * @param array $data
	 * @param $sheet
	 */
	function export(array $data, $sheet) {
		/** @var \Ryzom\Sheets\Client\CharacterSheet[] $data */
		echo "+ exporting $sheet\n";

		$export = [];

		/** @var int $id */
		foreach ($data as $id => $creature) {
			$key = $this->sheetIds->getSheetIdName($id, false);

			$array = array(
				'sheetid' => $key,
				'gender' => (int)$creature->Gender,
				'race' => (string)$creature->Race,
				'fame' => (string)$creature->Fame,
				'speed' => round($creature->MaxSpeed, 1),
				'region_force' => (int)$creature->RegionForce,
				'force_level' => (int)$creature->ForceLevel,
				'level' => (int)$creature->Level,
				'selectable' => (int)$creature->Selectable,
				'talkable' => (int)$creature->Talkable,
				'attackable' => (int)$creature->Attackable,
				'givable' => (int)$creature->Givable,
				'mountable' => (int)$creature->Mountable,
				'display_in_radar' => (int)$creature->DisplayInRadar,
			);

			$dataset = \RyzomExtra::get_dataset_name('creature', $key);

			$export[$dataset][$key] = $array;
		}

		foreach($export as $dataset => $array) {
			$this->_serializeInto($array, $dataset);
		}
	}
}

