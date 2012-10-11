<?php
	error_reporting(E_ALL);

	require_once('../ryzom_extra.php');

	$info = ryzom_building_info(182452286);
	print_r($info);
	$street = ryzom_translate($info['place'], 'en');
	$town = ryzom_translate($info['city'], 'en');
	$land = ryzom_translate($info['continent'], 'en');
	echo "Location: $land, $town, $street\n";

	// verify all location sheetid's for typos
	$buildings = include('../resources/buildings.inc.php');
	foreach($buildings as $b_id=>$b_info){
		try{
			$street = ryzom_translate($b_info['place'], 'en');
			$town = ryzom_translate($b_info['city'], 'en');
			$land = ryzom_translate($b_info['continent'], 'en');
			if(is_unknown($street) || is_unknown($town) || is_unknown($land)){
				echo "- [$b_id]: $land, $town, $street\n";
			}else{
				echo "+ $land, $town, $street\n";
			}
		}catch(Exception $ex){
			// if '.place' is mistyped
			echo ".suffix error [".$ex->getMessage()."]\n";
		}
	}

function is_unknown($str){
	if(substr($str, 0, 10) == 'NotFound:(' || substr($str, 0, 8) == 'Unknown:'){
		return true;
	}
	return false;
}
