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

	function __construct($path) {
		$this->path = $path;
	}

	/**
	 * @param array $data
	 * @param string $lang
	 */
	function export(array $data, $lang) {
		foreach ($data as $sheet => $array) {
			if ($sheet == 'sitem') {
				$sheet = 'item';
			}
			$filename = sprintf('%s/words_%s_%s.serial', $this->path, $lang, $sheet);

			file_put_contents($filename, serialize($array));
		}
	}
}

