<?php

class RyzomExtraSkilltreeTest extends \PHPUnit\Framework\TestCase
{
    public function test_skilltree()
    {
        $i = ryzom_skilltree();
        $this->assertIsArray($i);
        $this->assertArrayHasKey('sc', $i);
        $this->assertIsArray($i['sc']);
        $this->assertArrayHasKey('skill_id', $i['sc']);
    }
}
