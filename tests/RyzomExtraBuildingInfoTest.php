<?php

class RyzomExtraBuildingInfoTest extends \PHPUnit\Framework\TestCase
{
	public function test_building_info() {
		$b = ryzom_building_info(183500870);
		$this->assertEquals('g', $b['type']);
		$this->assertEquals('place_zora.place', $b['city']);
	}

	public function test_building_info_string_id() {
		$b = ryzom_building_info('183500870');
		$this->assertEquals('g', $b['type']);
		$this->assertEquals('place_zora.place', $b['city']);
	}
}
