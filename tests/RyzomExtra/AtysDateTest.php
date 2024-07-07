<?php

namespace RyzomExtra;

class AtysDateTest extends \PHPUnit\Framework\TestCase
{
    const shardName = 'atys';
    const shortShardName = 'atys';
    // todo: 64bit numbers here, might break on 32bit
    const shardTick = 279979598;
    const shardSync = 1363958744;
    //
    const dateString = 'Prima, Mystia 01, 2nd AC 2572';
    const dateYear = 2572;
    const dateCycle = 2;
    const dateSeason = 4;
    const dateMonth = 11;
    const dateWeek = 1;
	const dateDay = 1;
    //
    const seasonName = 'Winter';
    const monthName = 'Mystia';
    const dayName = 'Prima';
    //
    const timeHour = 0;
    const timeMinutes = 13;
	const timeStringHour = '00h';
	const timeStringHourMin = '00:13';

    /**
     * @var AtysDateTime
     */
    protected $atysDate;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() : void
    {
        // set mock function return values
        //time($this->shardSync);
        //ryzom_time_tick($this->shortShardName, $this->shardTick);

        // initialize
        $this->atysDate = new AtysDateTime('en');
        $this->atysDate->setGameCycle(self::shardTick);
    }

    public function testSetLanguage()
    {
        $this->assertEquals('Winter', $this->atysDate->getSeasonName());

        $this->atysDate->setLanguage('fr');
        $this->assertEquals('en Hiver', $this->atysDate->getSeasonName());
    }

    public function testSetTick()
    {
        $this->atysDate->setGameCycle(1234);
        $this->assertEquals(1234, $this->atysDate->getGameCycle());
    }

    /**
     * @dataProvider fixtureFormatDate
     */
    public function testFormatDate($showHour, $showMin, $expected)
    {
        $this->assertEquals($expected, $this->atysDate->formatDate($showHour, $showMin));
    }

    /**
     * @return array
     */
    static public function fixtureFormatDate()
    {
        return array(
            array(true, true, sprintf('%02d:%02d - %s', self::timeHour, self::timeMinutes, self::dateString)),
            array(true, false, sprintf('%02dh - %s', self::timeHour, self::dateString)),
            array(false, true, self::dateString),
            array(false, false, self::dateString),
        );
    }

    public function testGetYear()
    {
        $this->assertEquals(self::dateYear, $this->atysDate->getYear());
    }

    public function testGetCycle()
    {
        $this->assertEquals(self::dateCycle, $this->atysDate->getCycle());
    }

    public function testGetSeason()
    {
        $this->assertEquals(self::dateSeason, $this->atysDate->getSeason());
        $this->assertEquals(self::seasonName, $this->atysDate->getSeasonName());
    }

    public function testGetMonth()
    {
        $this->assertEquals(self::dateMonth, $this->atysDate->getMonth());
        $this->assertEquals(self::monthName, $this->atysDate->getMonthName());
    }

    public function testGetWeek()
    {
        $this->assertEquals(self::dateWeek, $this->atysDate->getWeek());
    }

    public function testGetDate()
    {
        $this->assertEquals(self::dateDay, $this->atysDate->getDate());
    }

    public function testGetDay()
    {
        $this->assertEquals(self::dateDay, $this->atysDate->getDay());
        $this->assertEquals(self::dayName, $this->atysDate->getDayName());
    }

    public function testGetHours()
    {
        $this->assertEquals(self::timeHour, $this->atysDate->getHours());
    }

    public function testGetMinutes()
    {
        $this->assertEquals(self::timeMinutes, $this->atysDate->getMinutes());
    }

    public function testToDateString()
    {
        $this->assertEquals(self::dateString, $this->atysDate->toDateString());
    }

    public function testMagicToString()
    {
        $this->assertEquals(self::dateString, (string) $this->atysDate);
    }

    public function testToTimeString()
    {
        $this->assertEquals(self::timeStringHour, $this->atysDate->toTimeString(false));
        $this->assertEquals(self::timeStringHourMin, $this->atysDate->toTimeString(true));
    }

    public function testParse()
    {
        $this->expectException('\RuntimeException', 'Not implemented');

        $this->atysDate->parse(self::dateString);
    }

    /**
     * @dataProvider seasonMonthNameDataProvider
     */
    public function testGetSeasonMonthName($season, $month, $expected)
    {
        $got = $this->atysDate->getSeasonMonthName($season, $month);
        $this->assertEquals($expected, $got);
    }

    /**
     * @return array
     */
    static public function seasonMonthNameDataProvider()
    {
        return array(
            // Spring
            array(1, 1, 'Winderly'),
            array(1, 2, 'Germinally'),
            array(1, 3, 'Folially'),
            // Summer
            array(2, 1, 'Floris'),
            array(2, 2, 'Medis'),
            array(2, 3, 'Thermis'),
            // Autumn
            array(3, 1, 'Harvestor'),
            array(3, 2, 'Frutor'),
            array(3, 3, 'Fallenor'),
            // Winter
            array(4, 1, 'Pluvia'),
            array(4, 2, 'Mystia'),
            array(4, 3, 'Nivia'),
        );
    }
}
