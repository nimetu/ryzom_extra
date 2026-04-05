<?php

class RyzomExtraCreatureInfoTest extends \PHPUnit\Framework\TestCase
{
    public function test_creature_info()
    {
        $i = ryzom_creature_info('trfa1');
        $this->assertIsArray($i);
    }

    public function test_creature_info_with_sheet()
    {
        $i = ryzom_creature_info('trfa1.creature');
        $this->assertIsArray($i);
    }

    public function test_creature_info_with_invalid_sheet()
    {
        $i = ryzom_creature_info('invalid.creature');
        $this->assertFalse($i);
    }
}
