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

class VisualSlotExport implements ExportInterface {

	/** @var SheetId */
	protected $sheetIds;

	protected $path;

	/** @var EncoderInterface */
	protected $encoder;

	public function __construct(SheetId $sheetIds, $path, EncoderInterface $encoder) {
		$this->sheetIds = $sheetIds;
		$this->path = $path;
		$this->encoder = $encoder;
	}

	/** {@inheritdoc} */
	public function export(array $data, $sheet) {
		$cache = array();

		/** @var array<int,int> $sheets */
		foreach($data as $slot => $sheets){
			foreach($sheets as $index => $sheetid){
				$cache[$slot][$index] = $this->sheetIds->getSheetIdName($sheetid);
			}
		}

		$this->_serializeInto($cache, 'visual_slot');
	}

	protected function _serializeInto(array $data, $name) {
		$ext = $this->encoder->name();
		$fileName = "{$this->path}/{$name}.{$ext}";
		echo "+ saving {$fileName}\n";
		file_put_contents($fileName, $this->encoder->encode($data));
	}
}
