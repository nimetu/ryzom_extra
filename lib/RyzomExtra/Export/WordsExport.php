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

	function __construct($path, EncoderInterface $encoder) {
		$this->path = $path;

		$this->encoder = $encoder;
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

			$ext = $this->encoder->name();
			$filename = "{$this->path}/words_{$lang}_{$sheet}.{$ext}";

			file_put_contents($filename, $this->encoder->encode($array));
		}
	}
}

