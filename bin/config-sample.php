<?php

$config = array();

// Ryzom client data directory location
// with .packed_sheets and .bnp files
$config['data.path'] = '/srv/ryzom-client/data';

// RyzomExtra directory for .serial files
$config['cache.path'] = __DIR__.'/../resources/sheets-cache';

return $config;
