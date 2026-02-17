<?php

class RyzomExtraSbrickInfoTest extends \PHPUnit\Framework\TestCase
{
	public function test_sbrick_info() {
		$i = ryzom_sbrick_info('bfcb01');
		$this->assertIsArray($i);
		$this->assertArrayHasKey('sheet_id', $i);
	}

	public function test_sbrick_info_with_sbrick() {
		$i = ryzom_sbrick_info('bfcb01.sbrick');
		$this->assertIsArray($i);
		$this->assertArrayHasKey('sheet_id', $i);
	}

	public function test_sbrick_info_with_invalid_sheet() {
		$i = ryzom_sbrick_info('!invalid!.sbrick');
		$this->assertFalse($i);
	}
}


