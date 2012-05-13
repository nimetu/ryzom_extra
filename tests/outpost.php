<?php

require_once '../ryzom_extra.php';

$sheetNames = array(
	array('fyros_outpost_04.outpost', 'Malmont Farm'),
	array('marauder_light_melee_fighter_b.outpost_squad', 'Marauder Warriors Squad'),
	array('driller_bountybeaches_kami_u1_100a.outpost_building', 'Offering for Tree-Bore'),
);

foreach($sheetNames as $array){
	$sheetName = $array[0];
	$expected = $array[1];
	$text = ryzom_translate($sheetName, 'en');
	$result = $text === $expected ? ' OK ' : 'FAIL';
	printf("%s %s - %s\n",$result, $sheetName, $text);
}

