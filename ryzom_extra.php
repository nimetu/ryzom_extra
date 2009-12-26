<?php
// 
// Copyright (c) 2009 Meelis Mägi <nimetu@gmail.com>
//
// Copying and distribution of this file, with or without modification,
// are permitted in any medium without royalty provided the copyright
// notice and this notice are preserved.  This file is offered as-is,
// without any warranty.
//

/**
 * NOTE: this file uses a lot of memory. One language + item + resource stats is around 75MiB of memory
 *       might be best to export data sets to database if php memory is limited
 */
//error_reporting(E_ALL);

// patch where to find data sets
define('RYZOM_EXTRA_PATH', dirname(__FILE__));

class RyzomExtra {
	// Record type - ['type']
	const TYPE_ARMOR           = 0x02;
	const TYPE_MELEE           = 0x03;
	const TYPE_RANGE           = 0x04;
	const TYPE_AMMO            = 0x05;
	const TYPE_RESOURCE        = 0x06;
	const TYPE_SHIELD          = 0x07;
	const TYPE_TOOLS           = 0x08;
	const TYPE_PICK            = 0x09;
	const TYPE_JEWEL           = 0x0f;
	const TYPE_TPTICKET        = 0x15;
	const TYPE_XPCAT           = 0x24;
	const TYPE_CASINO          = 0x28;
	//
	// Item type - ['item_type']
	const ITEM_DAGGER            = 0;
	const ITEM_SWORD             = 1;
	const ITEM_MACE              = 2;
	const ITEM_AXE               = 3;
	const ITEM_SPEAR             = 4;
	const ITEM_STAFF             = 5;
	const ITEM_2H_SWORD          = 6;
	const ITEM_2h_AXE            = 7;
	const ITEM_PIKE              = 8;
	const ITEM_2H_MACE           = 9;
	//
	const ITEM_AUTOLAUNCHER      = 10;
	const ITEM_BOWRIFLE          = 11;
	const ITEM_LAUNCHER          = 12;
	const ITEM_PISTOL            = 13;
	const ITEM_BOWPISTOL         = 14;
	const ITEM_RIFLE             = 15;
	//
	const ITEM_AUTOLAUNCHER_AMMO = 16;
	const ITEM_BOWRIFLE_AMMO     = 17;
	const ITEM_LAUNCHER_AMMO     = 18;
	const ITEM_PISTOL_AMMO       = 19;
	const ITEM_BOWPISTOL_AMMO    = 20;
	const ITEM_RIFLE_AMMO        = 21;
	//
	const ITEM_SHIELD            = 22;
	const ITEM_BUCKLER           = 23;
	//
	const ITEM_LA_BOOTS          = 24;
	const ITEM_LA_GLOVES         = 25;
	const ITEM_LA_PANTS          = 26;
	const ITEM_LA_SLEEVES        = 27;
	const ITEM_LA_VEST           = 28;
	//
	const ITEM_MA_BOOTS          = 29;
	const ITEM_MA_GLOVES         = 30;
	const ITEM_MA_PANTS          = 31;
	const ITEM_MA_SLEEVES        = 32;
	const ITEM_MA_VEST           = 33;
	//
	const ITEM_HA_BOOTS          = 34;
	const ITEM_HA_GLOVES         = 35;
	const ITEM_HA_PANTS          = 36;
	const ITEM_HA_SLEEVES        = 37;
	const ITEM_HA_VEST           = 38;
	const ITEM_HA_HELMET         = 39;
	//
	const ITEM_ANKLET            = 40;
	const ITEM_BRACELET          = 41;
	const ITEM_DIADEM            = 42;
	const ITEM_EARRING           = 43;
	const ITEM_PENDANT           = 44;
	const ITEM_RING              = 45;
	//
	const ITEM_PICK              = 46;
	const ITEM_ARMOR_CTOOL       = 47;
	const ITEM_AMMO_CTOOL        = 48;
	const ITEM_MELEE_CTOOL       = 49;
	const ITEM_RANGE_CTOOL       = 50;
	const ITEM_JEWEL_CTOOL       = 51;
	const ITEM_TOOL_CTOOL        = 52;
	// FIXME: 53
	const ITEM_MEKTOUB_PACKER    = 54;
	const ITEM_MEKTOUB_MOUNT     = 55;
	const ITEM_FORAGE_BALE       = 56; // mek food
	const ITEM_MAGIC_AMPLIFIER   = 57;
	// 58 - hom hairstyle
	// 59 - hom hair color
	// 60 - hom tatoo
	// 61 - hof hairstyle
	// 62 - hof hair color
	// 63 - hof tatoo
	const ITEM_T_65              = 65; // sap recharge, casino ticker/token/title 
	const ITEM_OTHER             = 66; // mats
	//
	// weapon/ammo damage
	const DMG_SLASH  = 0;
	const DMG_PIERCE = 1;
	const DMG_SMASH  = 2;
	//
	// bitfield: craft resource type - resource usually has 2 or more bits set
	// CODE: if((mpft & (1<<MPFT_BLADE)) != 0) // matches blade (shell, wondermats, kitin larva, ..)
	// SQL: WHERE (MPFT & (1<<MPFT_BLADE))<>0
	const MPFT_BLADE           = 0;
	const MPFT_HAMMER          = 1;
	const MPFT_POINT           = 2;
	const MPFT_SHAFT           = 3;
	const MPFT_GRIP            = 4;
	const MPFT_COUNTERWEIGHT   = 5;
	const MPFT_TRIGGER         = 6;
	const MPFT_FIRING_PIN      = 7;
	const MPFT_BARREL          = 8;
	const MPFT_EXPLOSIVE       = 9;
	const MPFT_AMMO_JACKET     = 10;
	const MPFT_AMMO_BULLET     = 11;
	const MPFT_ARMOR_SHELL     = 12;
	const MPFT_LINING          = 13;
	const MPFT_STUFFING        = 14;
	const MPFT_ARMOR_CLIP      = 15;
	const MPFT_JEWEL_SETTING   = 16;
	const MPFT_JEWEL           = 17;
	const MPFT_BLACKSMITH_TOOL = 18;
	const MPFT_PESTLE_TOOL     = 19;
	const MPFT_SHARPENER_TOOL  = 20;
	const MPFT_TUNNELING_KNIFE = 21;
	const MPFT_JEWELRY_HAMMER  = 22;
	const MPFT_CAMPFIRE        = 23;
	const MPFT_CLOTHES         = 24;
	const MPFT_MAGIC_FOCUS     = 25;
	const MPFT_UNKNOWN         = 26;
	//
	static $mpft_to_bit = array(
		'mpftMpL'   => self::MPFT_BLADE,
		'mpftMpH'   => self::MPFT_HAMMER,
		'mpftMpP'   => self::MPFT_POINT,
		'mpftMpM'   => self::MPFT_SHAFT,
		'mpftMpG'   => self::MPFT_GRIP,
		'mpftMpC'   => self::MPFT_COUNTERWEIGHT,
		'mpftMpGA'  => self::MPFT_TRIGGER,
		'mpftMpPE'  => self::MPFT_FIRING_PIN,
		'mpftMpCA'  => self::MPFT_BARREL,
		'mpftMpE'   => self::MPFT_EXPLOSIVE,
		'mpftMpEN'  => self::MPFT_AMMO_JACKET,
		'mpftMpPR'  => self::MPFT_AMMO_BULLET,
		'mpftMpCR'  => self::MPFT_ARMOR_SHELL,
		'mpftMpRI'  => self::MPFT_LINING,
		'mpftMpRE'  => self::MPFT_STUFFING,
		'mpftMpAT'  => self::MPFT_ARMOR_CLIP,
		'mpftMpSU'  => self::MPFT_JEWEL_SETTING,
		'mpftMpED'  => self::MPFT_JEWEL,
		'mpftMpBT'  => self::MPFT_BLACKSMITH_TOOL,
		'mpftMpPES' => self::MPFT_PESTLE_TOOL,
		'mpftMpSH'  => self::MPFT_SHARPENER_TOOL,
		'mpftMpTK'  => self::MPFT_TUNNELING_KNIFE,
		'mpftMpJH'  => self::MPFT_JEWELRY_HAMMER,
		'mpftMpCF'  => self::MPFT_CAMPFIRE,
		'mpftMpVE'  => self::MPFT_CLOTHES,
		'mpftMpMF'  => self::MPFT_MAGIC_FOCUS,
		'mpft'      => self::MPFT_UNKNOWN,    // [Undefined Raw Material Target]
	);
	// resource grade
	const GRADE_BASIC         = 20; // average, plain
	const GRADE_FINE          = 35; // prime
	const GRADE_CHOICE        = 50; // select
	const GRADE_EXCELLENT     = 65; // superb
	const GRADE_SUPREME       = 80; // magnificient
	//
	// item quality - this is actually texture id used. 
	const GRADE_BQ            = 0;
	const GRADE_MQ            = 1; // also tekorn/maga/greslin/armilo
	const GRADE_HQ            = 2; // also vedice/cheng/egiros/rubbarn
	//
	// resource ecosystem
	const ECO_COMMON          = 0; // basic/fine
	const ECO_DESERT          = 1;
	const ECO_FOREST          = 2;
	const ECO_LAKE            = 3;
	const ECO_JUNGLE          = 4;
	//                        = 5;
	const ECO_PR              = 6;
	//	
	// item race
	const RACE_COMMON         = 0; // outpost
	const RACE_FYROS          = 1; // desert
	const RACE_MATIS          = 2; // forest
	const RACE_TRYKER         = 3; // lake
	const RACE_ZORAI          = 4; // jungle
	const RACE_REFUGEE        = 5;
	const RACE_NPC            = 6; // pr
	const RACE_KAMI           = 7;
	const RACE_KARA           = 8;
	//
	// item or resource color
	const COLOR_RED           = 0;
	const COLOR_BEIGE         = 1;
	const COLOR_GREEN         = 2;
	const COLOR_TURQUOISE     = 3;
	const COLOR_BLUE          = 4;
	const COLOR_PURPLE        = 5;
	const COLOR_WHITE         = 6;
	const COLOR_BLACK         = 7;
	//
}

/**
 * Get $sheetid translation from .suffix language file.
 * Include language file on first run and cache it.
 *
 * NOTE: sheetid is converted to lowercase.
 *       line breaks must be handled separately. they marked as "\n"
 *
 * @param string sheetid
 * @param string lang
 * @param mixed $index for titles 0=male and 1=female. 
 *                     for anything else 'name', 'p', 'description', 'tooltip' (depends on sheetid type)
 *
 * @return string translated text, error message if language file or sheet id is not found
 */
function ryzom_translate($sheetid, $lang, $index=0){
	// memory usage for 1 language is around:
	// 4.7MiB creature, 70KiB faction, 8.5MiB item, 800KiB outpost, 600KiB place, 6MiB sbrick, 1MiB skill, 5MiB sphrase, 2MiB title, 4MiB uxt
	static $cache=array(); 
	
	// break up sheetid
	$_id = strtolower($sheetid);
	$_ext=strtolower(substr(strrchr($_id, '.'), 1));
	if($_ext===false || $_ext==''){
		$_ext='title'; // 'title' should be only one without 'dot' in sheetid
	}else{
		$_id=substr($_id, 0, strlen($_id)-strlen($_ext)-1);
	}

	// remap
	if($_ext=='sitem') $_ext='item';

	// 'Neutral' is not included in faction translation, so do it here
	if($_ext=='faction' && $_id=='neutral'){
		if($lang=='fr') {
			return 'Neutre';
		}else{
			return 'Neutral';
		}
	}

	// include translation file if needed
	if(!isset($cache[$_ext][$lang])){
		// use serialize/unserialize saves lot of memory
		$file = sprintf('%s/data/words_%s_%s.serial', RYZOM_EXTRA_PATH, $lang, $_ext);
		$cache[$_ext][$lang]=ryzom_extra_load_dataset($file);
	}


	// check if translation is there
	if(!isset($cache[$_ext][$lang][$_id])){
		return 'NotFound:('.$_ext.')'.$lang.'.'.$sheetid;
	}

	// return translation - each may have different array 'key' for translation
	$word=$cache[$_ext][$lang][$_id];
	switch($_ext){
		case 'creature': // keys name and p
			// fall thru
		case 'faction' : // keys name, member
			// fall thru
		case 'item'    : // keys name, p, description
			// fall thru
		case 'outpost' : // keys name, description
			// fall thru
		case 'place'   : // keys name
			// fall thru
		case 'sbrick'  : // keys name, p, description, tooltip
			// fall thru
		case 'skill'   : // keys name, p, description
			// fall thru
		case 'sphrase' : // keys name, p, description
			if(isset($word[$index])) return $word[$index];
			// fall back to 'name' index
			return $word['name'];
		case 'title'   : // keys name, women_name
			if((int) $index==0){
				return $word['name'];
			}else{
				return $word['women_name'];
			}
		// ui???? translations
		case 'uxt': // 
			return $word['name']; 
	}
	// should never reach here, but incase it does...
	return 'Unknown:'.$_ext.'.'.$_id;
}

/**
 * Return building info based building id from API XML file
 * If building_id is unknown, then return empty array
 *
 * @param int $building_id
 * @return array
 */
function &ryzom_building_info($building_id){
	static $cache=array();
	if(empty($cache)){
		$file= sprintf('%s/data/buildings.inc.php', RYZOM_EXTRA_PATH);
		if(!file_exists($file)){
			throw new Exception('Date file ['.$file.'] not found');
		}
		$cache=include($file);
	}
	if(!isset($cache[$building_id])){
		$result=array();
	}else{
		$result=$cache[$building_id];
	}
	return $result;
}

/**
 * Returns sheetid details
 *  
 * @param $sheetid - with or without '.sitem'
 * @param $extra   - for items, also include craft plan to '_craftplan' index
 *                   for resources, include stats to '_stats' index
 * @return array
 */
function &ryzom_item_info($sheetid, $extra=false){
	static $cache=array(); // ~ 20MiB, items
	
	// include data file if needed
	if(empty($cache)){
		// use serialize/unserialize saves lot of memory
		$file = sprintf('%s/data/items.serial', RYZOM_EXTRA_PATH);
		$cache=ryzom_extra_load_dataset($file);
	}
	
	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sitem$/', $_id, $m)){
		$_id=$m[1];
	}
	
	if(!isset($cache[$_id])){
		$result=false;
		return $result;
	}
	$result=$cache[$_id];
	
	// fix some id's
	if(isset($result['craftplan'])) $result['craftplan'].='.sbrick';
	if(isset($result['skill'])) $result['skill'].='.skill';
	$result['sheetid'].='.sitem';
	
	// if item type is Resource, then also include stats
	if($extra==true){
		if($result['type']==RyzomExtra::TYPE_RESOURCE){
			$result['_stats']=ryzom_resource_stats($_id);
		}else if(isset($result['craftplan'])){
			$result['_craftplan']=ryzom_craftplan($result['craftplan']);
		}
	}
	
	return $result;
}

/**
 * Return resource craft stats like durability/lightness, etc
 * 
 * @param $sheetid - with or without '.sitem'
 * @return mixed - FALSE if $sheetid not found
 */
function &ryzom_resource_stats($sheetid){
	static $cache;// ~20MiB, resource stats cache
	
	if(empty($cache)){
		$file=sprintf('%s/data/resource_stats.serial', RYZOM_EXTRA_PATH);
		$cache=ryzom_extra_load_dataset($file);
	}
	
	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sitem$/', $_id, $m)){
		$_id=$m[1];
	}
	
	if(isset($cache[$_id])){
		$result=$cache[$_id]['stats'];
	}else{
		$result=false;
	}
	return $result;
}

/**
 * Return craft plan
 * 
 * @param $sheetid - with or without '.sbrick'
 * @return unknown_type
 */
function &ryzom_craftplan($sheetid){
	static $cache=array();
	if(empty($cache)){
		$file=sprintf('%s/data/craftplan.serial', RYZOM_EXTRA_PATH);
		$cache=ryzom_extra_load_dataset($file);
	}
	
	$_id=strtolower($sheetid);
	if(preg_match('/^(.*)\.sbrick$/', $_id, $m)){
		$_id=$m[1];
	}
	
	if(isset($cache[$_id])){
		$result=$cache[$_id];
	}else{
		$result=false;
	}
	return $result;
}

/**
 * Return unformatted skilltree list
 * 
 * @return unknown_type
 */
function ryzom_skilltree(){
	static $cache=array();
	if(empty($cache)){
		$file=sprintf('%s/data/skilltree.serial', RYZOM_EXTRA_PATH);
		$cache=ryzom_extra_load_dataset($file);
	}
	
	return $cache;
}

/**
 * Loads dataset and returns result.
 * Does not unmask unserialize/file_get_content warning/notice's
 * 
 * throw Exception if file not found
 *  
 * @param $file file name with full path
 * @return mixed
 */
function &ryzom_extra_load_dataset($file){
	if(file_exists($file)){
		$result=unserialize(file_get_contents($file));
	}else{
		throw new Exception('Data file ['.$file.'] not found');
	}
	return $result;
}
