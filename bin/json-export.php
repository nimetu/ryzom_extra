<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2012 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomExtra.
//
// RyzomExtra is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomExtra is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

if (PHP_SAPI !== 'cli') {
    die('This script must be run from command-line');
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

use RyzomExtra\Export\Encoder\JsonEncoder;
use RyzomExtra\Export\EncoderInterface;

$config = require_once __DIR__ . '/config.php';
$config['cache.path'] = realpath($config['cache.path']);

$outPath = dirname(__DIR__) . '/resources-json';
if (!makeDirectoryIfNotExists($outPath) || !makeDirectoryIfNotExists($outPath . '/sheets-cache')) {
    exit(1);
}

$encoder = new JsonEncoder;

// convert serialized resources
$files = glob($config['cache.path'] . '/*.serial');
convert_sheets($files, $outPath . '/sheets-cache', $encoder);

// convert php files.
// rename output file if 'php => json' kv pair is given.
$phpFiles = [
    'buildings.inc.php' => 'buildings.json',
];
$incPath = dirname($config['cache.path']);
convert_php($phpFiles, $outPath, $encoder, $incPath);

exit;

function convert_sheets(array $files, string $outPath, EncoderInterface $encoder): void
{
    $ext = $encoder->name();
    foreach ($files as $file) {
        $info = pathinfo($file);
        $outFile = $info['filename'] . '.' . $ext;
        $data = unserialize(file_get_contents($file));
        file_put_contents($outPath . '/' . $outFile, $encoder->encode($data));
    }
}

function convert_php(array $files, string $outPath, EncoderInterface $encoder, string $incPath): void
{
    foreach ($files as $k => $file) {
        $info = pathinfo($file);

        $inFile = is_numeric($k) ? $file : $k;
        $outFile = is_numeric($k) ? $info['filename'] . '.json' : $file;

        $data = include $incPath . '/' . $inFile;
        file_put_contents($outPath . '/' . $outFile, $encoder->encode($data));
    }
}

function makeDirectoryIfNotExists($dir)
{
    if (!file_exists($dir) && !mkdir($dir, 0755, true)) {
        echo "ERROR: failed to create $dir\n";
        return false;
    }
    return true;
}
