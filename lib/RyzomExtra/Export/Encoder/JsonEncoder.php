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

namespace RyzomExtra\Export\Encoder;

use RyzomExtra\Export\EncoderInterface;

class JsonEncoder implements EncoderInterface {

	/** {@inheritdoc} */
	function encode(array $data) {
		$ret = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
		if ($ret === false) {
			echo "!! json encoding error\n";
			// monkey patch extended ascii chars that give utf8 errors
			// these does not appear to be used (16 in total)
			// 107155: filament_de_greslin_modifi<E9>_744.mp -> filament_de_greslin_modifié_744.mp
			// 108691: lichen_d<92>armilo_modifi<E9>_743.mp -> lichen_d’armilo_modifié_743.mp
			$fixes = array(
				"\xE9" => 'é',
				"\x92" => '’',
			);
			foreach($data as $key => $value) {
				$fixed = false;
				foreach($fixes as $find => $replace) {
					// test agains $value, but do replacement in $data
					if (strpos($value, $find) !== false) {
						$fixed = str_replace($find, $replace, $data[$key]);
						$data[$key] = $fixed;
					}
				}
				if ($fixed !== false) {
					echo "  $key: $value -> $fixed\n";
				}
			}

			$ret = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
			if ($ret === false) {
				foreach($data as $key => $value) {
					if (json_encode(array($key => $value)) !== false) {
						throw new \RuntimeException("Invalid value for json encoder: [$key]($value)");
					}
				}
			}
		}

		return $ret;
	}

	/** {@inheritdoc} */
	function name(){
		return 'json';
	}

}

