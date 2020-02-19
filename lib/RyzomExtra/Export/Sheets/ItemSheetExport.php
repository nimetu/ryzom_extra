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

use Ryzom\Common\EItemFamily;
use Ryzom\Sheets\Client\CMp;
use Ryzom\Sheets\Client\CMpItemPart;
use Ryzom\Sheets\Client\ItemSheet;
use Ryzom\Sheets\Client\SkilltreeSheet;

/**
 * Export ItemSheet to array using only few needed fields
 * Numeric sheetIds and skill's are converted into string.
 *
 * Content from 'item.packed_sheet' and 'sitem.packed_sheet'
 * must be exported at the same time or second export will
 * override first one.
 *
 * Creates files 'items.serial', 'resource_stats.serial'
 */
class ItemSheetExport extends AbstractSheetExport {

	/**
	 * @param array $data
	 * @param $sheet
	 */
	function export(array $data, $sheet) {
		echo "+ exporting $sheet\n";

		$skilltreeId = $this->sheetIds->getSheetId('skills.skill_tree');
		/** @var SkilltreeSheet  */
		$skilltree = $this->sheetsManager->findById($skilltreeId);

		$exportItems = array();
		$exportStats = array();

		$skipTypes = array(
			// haircut
			EItemFamily::UNDEFINED,
			EItemFamily::SERVICE,
			EItemFamily::COMMAND_TICKET,
		);

		/**
		 * @var $item ItemSheet
		 * @var int $id
		 */
		foreach ($data as $id => $item) {
			if (in_array($item->Family, $skipTypes)) {
				continue;
			}
			$key = $this->sheetIds->getSheetIdName($id, false);
			$array = array(
				'sheetid' => $key,
				'type' => $item->Family,
				'item_type' => $item->ItemType,
				'race' => $item->ItemOrigin,
				'quality' => $item->MapVariant,
				'bulk' => round($item->Bulk, 2),
				//'consumable' => $item->IsConsumable,
			);

			$iconKeys = array('main', 'back', 'over', 'over2');
			foreach ($iconKeys as $i => $k) {
				if (!empty($item->Icon[$i])) {
					$pos = strrpos($item->Icon[$i], '.tga');
					if($pos === false){
						$icon = $item->Icon[$i];
					}else{
						$icon = substr($item->Icon[$i], 0, $pos).'.png';
					}

					$array['icon'][$k] = $icon;
					if (!empty($item->IconColor[$i]) && $item->IconColor[$i] != -1) {
						$array['icon_color'][$k] = $item->IconColor[$i];
					}
				}
			}

			if (!empty($item->IconText)) {
				$array['txt'] = $item->IconText;
			}

			if ($item->CraftPlan > 0) {
				$array['craftplan'] = $this->sheetIds->getSheetIdName($item->CraftPlan, false);
			}

			switch ($item->Family) {
			case EItemFamily::ARMOR:
				//$array['armor_type'] = $item->Armor->ArmorType;
				// some armor has own color (like refugee armor plans)
				if ($item->Color >= 0) {
					$array['color'] = $item->Color;
				}
				break;
			case EItemFamily::MELEE_WEAPON:
				$array['skill'] = $item->MeleeWeapon->Skill;
				//$array['weapon_type'] = $item->MeleeWeapon->WeaponType;
				$array['damage'] = $item->MeleeWeapon->DamageType;
				$array['reach'] = $item->MeleeWeapon->MeleeRange;
				break;
			case EItemFamily::RANGE_WEAPON:
				$array['skill'] = $item->RangeWeapon->Skill;
				//$array['weapon_type'] = $item->RangeWeapon->WeaponType;
				//$array['range_weapon_type'] = $item->RangeWeapon->RangeWeaponType; // FIXME:
				break;
			case EItemFamily::AMMO:
				$array['skill'] = $item->Ammo->Skill;
				$array['damage'] = $item->Ammo->DamageType;
				break;
			case EItemFamily::RAW_MATERIAL:
				unset($array['race'], $array['quality'], $array['craftplan']);

				$isLooted = $this->_isMpLooted($item->Mp, $item->IconText);
				$isMission = $item->DropOrSell == 0 ? 1 : 0;

				$array['ecosystem'] = $item->Mp->Ecosystem;
				$array['grade'] = $item->Mp->StatEnergy;
				$array['mpft'] = $item->Mp->ItemPartBF;
				$array['color'] = $item->Mp->MpColor;
				$array['is_looted'] = $isLooted;
				$array['is_mission'] = $isMission;
				$array['index'] = $item->Mp->Family;
				//$array['mp_category'] = $item->Mp->MpCategory; // == 1
				//$array['harvest_skill'] = $item->Mp->HarvestSkill; // == 226
				if ($item->Mp->ItemPartBF > 0) {
					$array['stats'] = array(
						'sheetid' => $key,
						'stats' => $this->_exportResourceStats($item->Mp, $item->MpItemParts),
					);
				}
				break;
			case EItemFamily::SHIELD:
				//$array['shield_type'] = $item->Shield->ShieldType;
				break;
			case EItemFamily::CRAFTING_TOOL:
			case EItemFamily::HARVEST_TOOL:
			case EItemFamily::TAMING_TOOL:
				$array['skill'] = $item->Tool->Skill;
				//$array['_tool_type'] = $item->Tool->CraftingToolType;
				//$array['_command_range'] = $item->Tool->CommandRange;
				//$array['_max_donkey'] = $item->Tool->MaxDonkey;
				break;
			case EItemFamily::TELEPORT:
				unset($array['quality']);
				//$array['_teleport_type'] = $item->Teleport->Type;
				break;
			case EItemFamily::PET_ANIMAL_TICKET:
				//$array['_pet_slot'] = $item->Pet->Slot;
				break;
			case EItemFamily::GUILD_OPTION:
				//$array['_money_cost'] = $item->GuildOption->MoneyCost;
				//$array['_xp_cost'] = $item->GuildOption->XpCost;
				break;
			case EItemFamily::COSMETIC:
				//$array['_vp_value'] = $item->Cosmetic->VPValue;
				//$array['_gender'] = $item->Cosmetic->Gender;
				break;
			case EItemFamily::CONSUMABLE:
				//$array['_overdose_timer'] = $item->Consumable->OverdoseTimer;
				//$array['_consumption_time'] = $item->Consumable->ConsumptionTime;
				if (!empty($item->Consumable->Properties)) {
					$array['properties'] = $item->Consumable->Properties;
				}
				break;
			case EItemFamily::SCROLL:
				//$array['_texture'] = $item->Scroll->Texture;
				break;
			default:
				// nothing
			}

			foreach($item->Effect as $effect) {
				if (!empty($effect)){
					$array['effects'][] = $effect;
				}
			}

			// replace numeric skill code with string code
			if (isset($array['skill']) && !empty($skilltree)) {
				$skill = $skilltree->get($array['skill']);
				if (!empty($skill)) {
					$array['skill'] = strtolower($skill->SkillCode);
				}
			}

			// split resource stats from item array
			if (isset($array['stats'])) {
				$exportStats[$key] = $array['stats'];
				unset($array['stats']);
			}

			$exportItems[$key] = $array;
		}

		$this->_serializeInto($exportItems, 'items');
		$this->_serializeInto($exportStats, 'resource_stats');
	}

	/**
	 * @param \Ryzom\Sheets\Client\CMp $mp
	 * @param $txt
	 *
	 * @return int foraged=0, looted=1, unknown=-1
	 */
	private function _isMpLooted(CMp $mp, $txt) {
		$namesArray = array(
			'foraged' => array(
				'beng', 'hash', 'pha', 'sha', 'soo', 'zun',
				'adriel', 'becker', 'mitexi', 'oath', 'perfli',
				'anete', 'buo', 'dzao', 'shu', 'gulatc',
				'irin', 'koorin', 'pilan', 'dung', 'fung', 'glue', 'moon',
				'dante', 'enola', 'redhot', 'silver', 'visc',
				'capric', 'sarina', 'sauron', 'silvio',
				'big', 'cuty', 'horny', 'smart', 'splint',
				'abhaya', 'eyota', 'kachin', 'motega', 'tama',
				'nita', 'patee', 'scrath', 'tansy', 'yana',
				'kitin', // kitin larva
			),
			'looted' => array(
				// avian
				'igara', 'izam', 'yber',
				// carnivore
				'cloppr', 'cluttr', 'gingo', 'goari', 'hornch',
				'jugula', 'najab', 'ocyx', 'ragus', 'torbak',
				'tyranc', 'varinx', 'vorax', 'yetin', 'zerx',
				// flora
				'cratch', 'jubla', 'psykop', 'shooki', 'slaven', 'stinga',
				// herbivore
				'arana', 'arma', 'bawaab', 'bodoc', 'bolobi', 'capryn',
				'cray', 'frippo', 'gnoof', 'gubani', 'lumper', 'madaka',
				'messab', 'ploder', 'raspal', 'rendor', 'shalah', 'timari',
				'wombai', 'yelk', 'yubo',
				// javan
				'javing',
				// kitin
				'kiban', 'kidina', 'kinchr', 'kinrey', 'kipee', 'kipest',
				'kipuck', 'kirost', 'kizara', 'kizoar',
			),
			'unknown' => array(
				// corrup - corrupt moon ??
				// grand
				// mp - generic mats / faction mats ??
				'corrup', 'grand', 'mp',
			),
		);

		$txt = strtolower($txt);
		if (in_array($txt, $namesArray['foraged'])) {
			if ($mp->Family == 774) {
				return -1; // Supreme Kitin Sting, probably new type of mat that can be looted like kitin larva, only has one stat tho...
			} else {
				return 0;
			}
		} else if (in_array($txt, $namesArray['looted'])) {
			return 1;
		} else { // $namesArray['unknown']
			return -1;
		}
	}

	/**
	 * FIXME: needs OriginFilter too?
	 *
	 * @param \Ryzom\Sheets\Client\CMp $mp
	 * @param CMpItemPart[] $stats
	 *
	 * @return array
	 */
	private function _exportResourceStats(CMp $mp, array $stats) {
		$result = array();

		$mpftMap = $this->getMpftMap($mp->ItemPartBF);

		$statIndex = 0;
		foreach ($mpftMap as $bit => $name) {
			$statValues = $stats[$statIndex];

			// get stat keys that this mpft is using
			$statKeys = $this->getMpftStats($bit);
			$result[$name] = array();
			foreach ($statKeys as $key) {
				$index = $this->getStatIndex($key);
				$result[$name][$key] = $statValues->Stats[$index];
			}
			$statIndex++;
		}

		return $result;
	}

}
