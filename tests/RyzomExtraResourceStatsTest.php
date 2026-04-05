<?php

class RyzomExtraResourceStatsTest extends \PHPUnit\Framework\TestCase
{
    public function test_resource_stats_with_sitem()
    {
        $i = ryzom_resource_stats('m0006dxacb01.sitem');
        $this->assertIsArray($i);
        $this->assertArrayNotHasKey('sheetid', $i);
        $this->assertArrayHasKey('mpftMpG', $i);
    }

    public function test_resource_stats()
    {
        $i = ryzom_resource_stats('m0006dxacb01');
        $this->assertIsArray($i);
        $this->assertArrayNotHasKey('sheetid', $i);
        $this->assertArrayHasKey('mpftMpG', $i);
    }
}
