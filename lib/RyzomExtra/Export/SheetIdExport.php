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

use Nel\Misc\SheetId;

class SheetIdExport implements ExportInterface {

	function __construct(SheetId $sheetIds, $path) {
		$this->path = $path;
	}

	/**
	 * Export SheetId collection into separate smaller files
	 *
	 * @param array $data
	 * @param $sheet
	 */
	function export(array $data, $sheet) {
		$groups = array();

		// split full list into separate files
		foreach ($data as $id => $array) {
			$key = floor($id / 1000000);
			$groups[$key][$id] = array('name' => $array['name'], 'suffix' => $array['sheet']);
		}

		foreach ($groups as $key => $array) {
			$filename = sprintf('%s/%s-%02x.serial', $this->path, $sheet, $key);
			file_put_contents($filename, serialize($array));
		}
	}

}
