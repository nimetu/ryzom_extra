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
use Ryzom\Sheets\Client\SbrickSheet;
use Ryzom\Sheets\Client\CItemPartMps;
use Ryzom\Sheets\Client\CFormulaMps;
use Ryzom\Sheets\Client\ItemSheet;
use Ryzom\Sheets\Client\CRequiredSkill;

/**
 * Export SbrickSheet to array using only few needed fields
 * Numeric sheetIds and skill's are converted into string
 *
 * Creates files 'craftplan.serial', 'sbrick.serial'
 */
class SbrickSheetExport extends AbstractSheetExport {

	/**
	 * @param array $data
	 * @param $sheet
	 */
	function export(array $data, $sheet) {
		echo "+ exporting $sheet\n";

		$skilltreeId = $this->sheetIds->getSheetId('skills.skill_tree');
		$skilltree = $this->sheetsManager->findById($skilltreeId);

		$exportSbricks = array();
		$exportPlans = array();

		foreach ($data as $id => $sbrick) {
			/**
			 * @var int $id
			 * @var SbrickSheet $sbrick
			 */
			$key = $this->sheetIds->getSheetIdName($id, false);

			$array = array(
				'sheet_id' => $key,
				'brick_family' => $sbrick->BrickFamily,
				'index_in_family' => $sbrick->IndexInFamily,
				'level' => $sbrick->Level,
				'sabrina_cost' => $sbrick->SabrinaCost,
				'sabrina_relative_cost' => $sbrick->SabrinaRelativeCost,
				'action_nature' => $sbrick->ActionNature,
				'cast_time' => array($sbrick->MinCastTime, $sbrick->MaxCastTime),
				'range' => array($sbrick->MinRange, $sbrick->MaxRange),
				//'brick_required_flags' => $sbrick->BrickRequiredFlags,
				'sp_cost' => $sbrick->SPCost,
				'used_skills' => $sbrick->UsedSkills,
				'civ_restriction' => $sbrick->CivRestriction, // 6 == common, can be used by everyone
			);

			if (!empty($sbrick->ForbiddenDef)) {
				$array['forbidden_def'] = $sbrick->ForbiddenDef;
			}

			if (!empty($sbrick->ForbiddenExclude)) {
				$array['forbidden_exclude'] = $sbrick->ForbiddenExclude;
			}

			if (!empty($sbrick->RequiredBricks)) {
				foreach ($sbrick->RequiredBricks as $brick) {
					$array['required_bricks'][] = $this->sheetIds->getSheetIdName($brick, false);
				}
			}

			if ($sbrick->FactionIndex != -1) {
				$array['faction_index'] = $sbrick->FactionIndex;
				$array['min_fame_value'] = $sbrick->MinFameValue;
			}

			if ($sbrick->MagicResistType !== 5) {
				// 1 == desert, forest, lacustre, jungle, primaryRoot, 5 == none
				$array['magic_resist_type'] = $sbrick->MagicResistType;
			}

			$iconKeys = array('main', 'back', 'over', 'over2');
			foreach ($iconKeys as $i => $k) {
				if (!empty($sbrick->Icon[$i])) {
					$pos = strrpos($sbrick->Icon[$i], '.tga');
					if ($pos === false) {
						$icon = $sbrick->Icon[$i];
					} else {
						$icon = substr($sbrick->Icon[$i], 0, $pos).'.png';
					}
					$array['icon'][$k] = $icon;
					if (!empty($sbrick->IconColor[$i]) && $sbrick->IconColor[$i] != -1) {
						$array['icon_color'][$k] = $sbrick->IconColor[$i];
					}
				}
			}

			if (!empty($sbrick->MandatoryFamilies)) {
				$array['mandatory_families'] = $sbrick->MandatoryFamilies;
			}

			if (!empty($sbrick->OptionalFamilies)) {
				$array['optional_families'] = $sbrick->OptionalFamilies;
			}

			if (!empty($sbrick->ParameterFamilies)) {
				$array['parameter_families'] = $sbrick->ParameterFamilies;
			}

			if (!empty($sbrick->CreditFamilies)) {
				$array['credit_families'] = $sbrick->CreditFamilies;
			}

			if (!empty($sbrick->RequiredSkills)) {
				/** @var CRequiredSkill $skill */
				foreach ($sbrick->RequiredSkills as $skill) {
					$skillCode = strtolower($skilltree->get($skill->Skill)->SkillCode);
					$array['required_skills'][$skillCode] = $skill->Value;
				}
			}

			// brick has craft plan info
			if ($sbrick->FaberPlan->ItemBuilt > 0) {
				/** @var $item ItemSheet */
				$item = $this->sheetsManager->findById($sbrick->FaberPlan->ItemBuilt);

				$plan = array(
					'item_type' => $item->ItemType,
				);

				/** @var CItemPartMps $pmp */
				foreach ($sbrick->FaberPlan->ItemPartMps as $pmp) {
					$name = $this->getMpftName($pmp->FaberTypeFilter);
					$plan['mpft'][$name] = $pmp->Quantity;
				}

				/** @var CFormulaMps $fmp */
				foreach ($sbrick->FaberPlan->FormulaMps as $fmp) {
					$name = $this->sheetIds->getSheetIdName($fmp->ItemRequired);
					$plan['extra'][$name] = $fmp->Quantity;
				}

				$exportPlans[$key] = $plan;
			}

			$exportSbricks[$key] = $array;
		}

		$this->_serializeInto($exportSbricks, 'sbrick');
		$this->_serializeInto($exportPlans, 'craftplan');
	}
}
