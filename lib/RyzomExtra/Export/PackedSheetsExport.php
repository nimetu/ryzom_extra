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

use RyzomExtra\Export\Sheets\AbstractSheetExport;

class PackedSheetsExport extends AbstractSheetExport {

	/**
	 * @param array $data array from PackedSheets::getSheets()
	 * @param string $sheet sheet name
	 *
	 * @throws \RuntimeException
	 */
	function export(array $data, $sheet) {
		switch ($sheet) {
		case 'creature':
			$export = new Sheets\CharacterSheetExport($this->sheetIds, $this->sheetsManager, $this->path, $this->encoder);
			break;
		case 'item':
		case 'sitem':
			$export = new Sheets\ItemSheetExport($this->sheetIds, $this->sheetsManager, $this->path, $this->encoder);
			break;
		case 'skill_tree':
			$export = new Sheets\SkilltreeSheetExport($this->sheetIds, $this->sheetsManager, $this->path, $this->encoder);
			break;
		case 'sbrick':
			$export = new Sheets\SbrickSheetExport($this->sheetIds, $this->sheetsManager, $this->path, $this->encoder);
			break;
		case 'sphrase':
			// TODO: SphraseSheetExport()
			//var_dump($data);
			return;
		default:
			throw new \RuntimeException("Unknown sheet ($sheet)");
		}

		$export->export($data, $sheet);
	}

}
