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

namespace RyzomExtra\Export;

class WordsExport implements ExportInterface {

	/** @var EncoderInterface */
	protected $encoder;

	/** @var string */
	protected $path;

	function __construct($path, EncoderInterface $encoder) {
		$this->path = $path;

		$this->encoder = $encoder;
	}

	/**
	 * @param array $data
	 * @param string $sheet en|fr|de|es|ru language code
	 */
	function export(array $data, $sheet) {
		$lang = $sheet;
		foreach ($data as $sheetName => $array) {
			if ($sheetName == 'sitem') {
				$sheetName = 'item';
			}

			$ext = $this->encoder->name();
			$filename = "{$this->path}/words_{$lang}_{$sheetName}.{$ext}";

			// keep array sorted to minimize change diff
			ksort($array);
			file_put_contents($filename, $this->encoder->encode($array));
		}
	}
}

