<?php
echo "<pre>\n";
$mem=memory_get_usage();
$start = microtime(true);

require_once('../ryzom_extra.php');

$sheets = array(
	 998953,
	1997609,
	2930985,
	3995945,
	4003886,
	5014057,
	6030377,
	7057198,
	8024873,
	9109545,

	8584750,
	8854830,
	8889902,
);

foreach($sheets as $s){
	$sheet = ryzom_sheetid_bin($s);
	var_dump($sheet);
	//$name = ryzom_translate($sheet, 'en');
	//var_dump($name);
}

$end = microtime(true);

echo "\n
-- memory : ".(memory_get_usage()-$mem)."
-- time   : ".($end-$start)." seconds
";

echo "</pre>\n";
