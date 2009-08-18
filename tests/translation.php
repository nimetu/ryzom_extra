<?
	require_once('../ryzom_extra.php');
	
$ids=array(
	'caaga1.creature'=> array('name', 'p'),
	'cdagc3.creature'=> array('name', 'p'),
	'fyros.faction'  => array('name', 'member'),
	'icmm1pdl.sitem' => array('name', 'p', 'description'),
	'fyros_outpost_04.outpost' => array('name', 'description'),
	'region_majesticgarden.place' => array('name'),
	'bfma01.sbrick'  => array('name', 'p', 'description', 'description2'),
	'acuratebleedingshot.skill' => array('name', 'p', 'description'),
	'abf01.sphrase'  => array('name','p', 'description'),
	'light_armsman.title'  => array('name', 0, 1),
	'LanguageName.uxt' => array('name'),
);
foreach($ids as $sheetid=>$indices){
	echo $sheetid.":\n";
	foreach($indices as $i){
		echo '    '.$i.' => '.ryzom_translate($sheetid, 'en', $i)."\n";
	}
}
echo "\n";
