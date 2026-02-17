<?php

class RyzomExtraCraftplanTest extends \PHPUnit\Framework\TestCase
{
	public function test_craftplan() {
		$i = ryzom_craftplan('bcfmea01');
		$this->assertIsArray($i);
		$this->assertArrayHasKey('mpft', $i);
	}

	public function test_craftplan_with_sbrick() {
		$i = ryzom_craftplan('bcfmea01.sbrick');
		$this->assertIsArray($i);
		$this->assertArrayHasKey('mpft', $i);
	}

	public function test_craftplan_with_invalid_sheet() {
		$i = ryzom_craftplan('!invalid!.sbrick');
		$this->assertFalse($i);
	}
}



