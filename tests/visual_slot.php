<?php

require_once __DIR__.'/../ryzom_extra.php';

$result = ryzom_vs_sheet(1, 1);
var_dump($result);

$result = ryzom_vs_index(1, 'icfahv.sitem');
var_dump($result);
