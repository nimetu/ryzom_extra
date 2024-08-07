<?php

namespace RyzomExtra;

class GuildIconBuilderTest extends \PHPUnit\Framework\TestCase
{

    public function testDefault()
    {
        $iconBuilder = new GuildIconBuilder();
        $icon = $iconBuilder->getValue();
        $this->assertEquals('17', $icon);
    }

    public function testDecode()
    {
        $icon = '2260630401190946';
        $iconBuilder = new GuildIconBuilder($icon);

        $this->assertEquals(1, $iconBuilder->Background);
        $this->assertEquals(1, $iconBuilder->Symbol);
        $this->assertEquals(1, $iconBuilder->Inverted);
        $this->assertEquals(1, $iconBuilder->Color1Red);
        $this->assertEquals(1, $iconBuilder->Color1Green);
        $this->assertEquals(1, $iconBuilder->Color1Blue);
        $this->assertEquals(1, $iconBuilder->Color2Red);
        $this->assertEquals(1, $iconBuilder->Color2Green);
        $this->assertEquals(1, $iconBuilder->Color2Blue);
    }

    public function testEncode()
    {
        $iconBuilder = new GuildIconBuilder();
        $iconBuilder->Background = 0;
        $iconBuilder->Symbol = 0;

        $icon = $iconBuilder->getValue();

        $this->assertEquals('17', $icon);
    }

    public function testEncodeFull()
    {
        $iconBuilder = new GuildIconBuilder();
        $iconBuilder->Background = 1;
        $iconBuilder->Symbol = 1;
        $iconBuilder->Inverted = 1;
        $iconBuilder->Color1Red = 1;
        $iconBuilder->Color1Green = 1;
        $iconBuilder->Color1Blue = 1;
        $iconBuilder->Color2Red = 1;
        $iconBuilder->Color2Green = 1;
        $iconBuilder->Color2Blue = 1;

        $icon = $iconBuilder->getValue();

        $this->assertEquals('2260630401190946', $icon);
    }
}
