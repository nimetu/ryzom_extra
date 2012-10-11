<?php

require_once __DIR__.'/../vendor/autoload.php';

$code = '74600819328030280';

$icon = new RyzomExtra\GuildIconBuilder($code);
$render = new RyzomExtra\GuildIconRenderer($icon, __DIR__.'/../resources/guild-icon');
$render->setSize(64, 64);

file_put_contents($code.'.png', $render->asPng(9));

