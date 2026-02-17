<?php

class RyzomExtraSheetIdTest extends \PHPUnit\Framework\TestCase
{
	public function test_sheetid() {
		$this->assertEquals('zorai_heavy_vest_06_10.item', ryzom_sheetid_bin(3000065));
		$this->assertEquals('ckacf4.creature', ryzom_sheetid_bin(9000233));
		$this->assertEquals('ckacf4.creature', ryzom_sheetid_bin('9000233'));
	}
	public function test_sheetid_exception() {
		$this->expectException(\Exception::class);
		$this->assertSame(false, ryzom_sheetid_bin(1234567890));
	}
}
