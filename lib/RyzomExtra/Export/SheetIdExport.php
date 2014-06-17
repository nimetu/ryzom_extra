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

	/** @var EncoderInterface */
	protected $encoder;

	function __construct(SheetId $sheetIds, $path, EncoderInterface $encoder) {
		$this->path = $path;
		$this->encoder = $encoder;
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
		// reading them back in is a lot faster this way
		foreach ($data as $id => $array) {
			$key = floor($id / 1000000);
			$groups[$key][$id] = array('name' => $array['name'], 'suffix' => $array['sheet']);
		}

		$ext = $this->encoder->name();
		foreach ($groups as $key => $array) {
			$idx = sprintf("%02x", $key);
			$filename = "{$this->path}/{$sheet}-{$idx}.{$ext}";
			file_put_contents($filename, $this->encoder->encode($array));
		}
	}

}
