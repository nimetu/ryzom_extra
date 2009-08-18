<?
// 
// Copyright (c) 2009 Meelis Mägi <nimetu@gmail.com>
//
// Copying and distribution of this file, with or without modification,
// are permitted in any medium without royalty provided the copyright
// notice and this notice are preserved.  This file is offered as-is,
// without any warranty.
//

define('RYZOM_EXTRA_PATH', dirname(__FILE__));
define('RYZOM_EXTRA_CACHE_ID', '__ryzom_extra_cache');

// translation array
if(!isset($GLOBALS[RYZOM_EXTRA_CACHE_ID])){
	$GLOBALS[RYZOM_EXTRA_CACHE_ID]=array();
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
	if(!isset($GLOBALS[RYZOM_EXTRA_CACHE_ID][$_ext][$lang])){
		// use serialize/unserialize saves lot of memory
		$file = sprintf('%s/data/words_%s_%s.serial', RYZOM_EXTRA_PATH, $lang, $_ext);
		if(file_exists($file)){
			$ret=@unserialize(file_get_contents($file));
			if($ret!==false){
				$GLOBALS[RYZOM_EXTRA_CACHE_ID][$_ext][$lang] = $ret; 
			}
		}
	}


	// check if translation is there
	if(!isset($GLOBALS[RYZOM_EXTRA_CACHE_ID][$_ext][$lang][$_id])){
		return 'NotFound:('.$_ext.')'.$lang.'.'.$sheetid;
	}

	// return translation - each may have different array 'key' for translation
	$word=$GLOBALS[RYZOM_EXTRA_CACHE_ID][$_ext][$lang][$_id];
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
		case 'uxt': // not an array
			return $word; 
	}
	// should never reach here, but incase it does...
	return 'Unknown:'.$_ext.'.'.$_id;
}
