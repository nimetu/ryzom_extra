<?
echo "<pre>\n";
	$mem=memory_get_usage();

	require_once('../ryzom_extra.php');
	$items = array(
		'ictr1p_2',
		'icfr2a_2',
		'ictr2b_3',
		'ictr2r_3',
		'icokamr2a_1',
		'icmjd_3', 'icfm1sab', 'ictp2rb', 'icokamm1pd_1',
		'ixpca01', 'tp_kami_davae', 'tp_karavan_davae','casino_ticket',
		'm0006dxacb01', 'm0669ccodc01', 'm0208dxaca01'
	);
foreach($items as $sheetid){
	$item=ryzom_item_info($sheetid.'.sitem', true);
	$name=ryzom_translate($sheetid.'.sitem', 'en');
	echo 'item ['.$sheetid.'] = '.$name."\n";
	if(isset($item['skill']))     echo " - xp in skill= ".ryzom_translate($item['skill'], 'en')."\n";
	if(isset($item['craftplan'])) {
		echo " - craft plan = ".ryzom_translate($item['craftplan'], 'en')."\n";
		if($item['_craftplan']===false){
			echo " * craft plan was not found\n";
//			print_r($item);
		}else{
			$plan=$item['_craftplan'];
			if(!empty($plan['mpft'])){
				echo " + resources in craft plan \n";
				foreach($plan['mpft'] as $k=>$v){
					$r=ryzom_translate($k.'.uxt', 'en');
					printf("\t - %dx %s\n", $v, $r);
				}
			}
			if(!empty($plan['extra'])){
				echo "\t + extra items\n";
				foreach($plan['extra'] as $k=>$v){
					printf("\t - %dx %s\n", $v, ryzom_translate($k, 'en'));
				}
			}
		}
	}else{
//		print_r($item);
	}
}
echo "\n-- memory used: ".(memory_get_usage()-$mem)."\n";
echo "</pre>\n";
