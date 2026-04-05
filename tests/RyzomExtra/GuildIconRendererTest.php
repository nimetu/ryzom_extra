<?php

use RyzomExtra\GuildIconBuilder;
use RyzomExtra\GuildIconRenderer;

class GuildIconRendererTest extends \PHPUnit\Framework\TestCase
{
    // TODO: split up output() and applyFilter() for easier testing with mocks
    public function test_default_construct()
    {
        $b = new GuildIconBuilder();
        $r = new GuildIconRenderer($b);

        $png = $r->output();
        $w = imagesx($png);
        $h = imagesy($png);

        $this->assertEquals([64, 64], [$w, $h]);
    }

    public function test_setSize32()
    {
        $b = new GuildIconBuilder();
        $r = new GuildIconRenderer($b);
        $r->setSize('s');

        $png = $r->output();
        $w = imagesx($png);
        $h = imagesy($png);

        $this->assertEquals([32, 32], [$w, $h]);
    }

    public function test_setSize64()
    {
        $b = new GuildIconBuilder();
        $r = new GuildIconRenderer($b);
        $r->setSize('b');

        $png = $r->output();
        $w = imagesx($png);
        $h = imagesy($png);

        $this->assertEquals([64, 64], [$w, $h]);
    }

    public function test_setSizeInvalid()
    {
        $b = new GuildIconBuilder();
        $r = new GuildIconRenderer($b);
        $r->setSize('invalid');

        $png = $r->output();
        $w = imagesx($png);
        $h = imagesy($png);

        $this->assertEquals([64, 64], [$w, $h]);
    }

    public function test_asPng()
    {
        $b = new GuildIconBuilder();
        $r = new GuildIconRenderer($b);
        $r->setSize('b');

        $png = $r->asPng();
        $this->assertIsString($png);
        $this->assertEquals("\x89PNG\r\n\032\n", substr($png, 0, 8));

        $im = imagecreatefromstring($png);
        $w = imagesx($im);
        $h = imagesy($im);
        unset($im);

        $this->assertEquals([64, 64], [$w, $h]);
    }
}
