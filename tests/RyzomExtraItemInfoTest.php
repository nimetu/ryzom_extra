<?php

class RyzomExtraItemInfoTest extends \PHPUnit\Framework\TestCase
{
    public function test_item_info()
    {
        $i = ryzom_item_info('fy_helmet_01');
        $this->assertEquals('fy_helmet_01.sitem', $i['sheetid']);
    }

    public function test_item_info_with_sitem()
    {
        $i = ryzom_item_info('ka_helmet_01.sitem');
        $this->assertEquals('ka_helmet_01.sitem', $i['sheetid']);
    }

    public function test_item_info_with_craftplan()
    {
        $i = ryzom_item_info('icfr1b', true);
        $this->assertEquals('icfr1b.sitem', $i['sheetid']);
        $this->assertArrayHasKey('_craftplan', $i);
        $this->assertIsArray($i['_craftplan']);
        $this->assertArrayHasKey('item_type', $i['_craftplan']);
    }

    public function test_item_info_without_craftplan()
    {
        $i = ryzom_item_info('icfr1b', false);
        $this->assertEquals('icfr1b.sitem', $i['sheetid']);
        $this->assertArrayNotHasKey('_craftplan', $i);
    }

    public function test_item_info_with_stats()
    {
        $i = ryzom_item_info('m0006dxacb01', true);
        $this->assertEquals('m0006dxacb01.sitem', $i['sheetid']);
        $this->assertArrayHasKey('_stats', $i);
        $this->assertArrayNotHasKey('_craftplan', $i);
        $this->assertIsArray($i['_stats']);
        $this->assertArrayNotHasKey('mpfMpG', $i['_stats']);
    }
}
