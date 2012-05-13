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

use RyzomExtra\Export\ExportInterface;
use Nel\Misc\SheetId;
use Ryzom\Sheets\Client\SkilltreeSheet;
use Ryzom\Sheets\Client\CSkill;

/**
 * Export SkilltreeSheet to array.
 *
 * Creates file 'skilltree.serial'
 */
class SkilltreeSheetExport extends AbstractSheetExport {

	/**
	 * @param array $data
	 * @param string $sheet
	 */
	function export(array $data, $sheet) {
		echo "+ exporting $sheet\n";
		/** @var $skilltree SkilltreeSheet */
		$skilltree = $data[36]; // #36 == skills.skill_tree

		foreach ($skilltree->getSkills() as $skill) {
			/** @var $skill CSkill */
			$skillCode = strtolower($skill->SkillCode);
			$array = array(
				'max' => $skill->MaxSkillValue,
				'node_id' => $skill->Skill,
				'parent_node' => $skill->ParentSkill,
				'skill_id' => $skillCode,
			);
			$exportSkills[$skillCode] = $array;
		}

		$this->_serializeInto($exportSkills, 'skilltree');
	}
}
