<?php

require_once dirname(__DIR__) . '/ryzom_extra.php';

//$test = RyzomExtra::consumable_effects('test_conso.sitem', 10, 'en');
//var_dump($test);
//

$lang = 'en';

//if (false)
{
    echo "\n*\n* consumable effects\n*\n";
    $qArray = [10, 50, 250];
    $tArray = [
        'ipoc_bal.sitem' => true,
        'ipoc_con.sitem' => true,
        'ipoc_dex.sitem' => true,
        'ipoc_foc.sitem' => true,
        'ipoc_int.sitem' => true,
        'ipoc_met.sitem' => true,
        'ipoc_sap.sitem' => true,
        'ipoc_sta.sitem' => true,
        'ipoc_str.sitem' => true,
        'ipoc_wil.sitem' => true,
        'ipoc_wis.sitem' => true,

        'ipoc_con_10min.sitem' => true,

        'pvp_aura_saplife_c2' => true,
    ];

    test_items($tArray, $qArray, $lang);

    $qArray = [1, 3, 6];
    $tArray = [
        'rpjobitem_201_c0' => true, // SP_CHG_CHARAC
        'rpjobitem_202_c1' => false, // SAP_AURA, LIFE_AURA
        'rpjobitem_205_c1' => true, // SP_CHG_CHARAC
        'rpjobitem_207_c0' => true, // SP_MOD_CRAFT_SUCCESS

    ];

    test_items($tArray, $qArray, $lang);
}

//if (false)
{
    echo "\n*\n* special effects\n*\n";
    $tArray = [
        'icokamm1bm_1',
        'icokamm1bm_2',
        'icokamm2ms_1',
        'icokamm2ms_2',
        'icokamtammo_1',
        'icokamtammo_2',
        'icokamtforage_1',
        'icokamtforage_2',
    ];

    test_effects($tArray, $lang);
}

function test_effects($tArray, $lang)
{
    foreach ($tArray as $row) {
        echo "+ [{$row}]\n";
        $ret = RyzomExtra::special_effects($row, $lang);
        if (empty($ret)) {
            $ret = " - FAIL (empty response)";
        } else {
            $ret = strip_color(" " . join("\n  + ", str_replace("\n", "\n ", $ret)));

        }
        echo "$ret\n\n";
    }
}

function strip_color($s)
{
    return preg_replace("/@{[^}]+}/", '', $s);
}

function test_items($tArray, $qArray, $lang)
{
    foreach ($tArray as $sheet => $changes) {

        $info = $changes ? ' (changes by quality)' : '';

        $result = [];
        foreach ($qArray as $quality) {
            $ret = RyzomExtra::consumable_effects($sheet, $quality, $lang);
            $ret = strip_color("  + " . join("\n  + ", str_replace("\n", "\n    ", $ret)));
            if (empty($result)) {
                $status = '  OK  ';
            } elseif ($changes) {
                $status = isset($result[$ret]) ? ' FAIL ' : ' PASS ';
            } else {
                $status = isset($result[$ret]) ? ' PASS ' : ' FAIL ';
            }
            $result[$ret] = true;

            echo "[$status] $quality :: $sheet$info\n$ret\n";
        }
    }
}

