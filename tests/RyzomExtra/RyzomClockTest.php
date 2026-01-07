<?php

namespace RyzomExtra;

use PHPUnit\Framework\Attributes\DataProvider;

class RyzomClockTest extends \PHPUnit\Framework\TestCase
{

    /** @var RyzomClock */
    protected $ryzomClock;

    public function setUp() : void
    {
        $this->ryzomClock = new RyzomClock(0, false);

    }

    public function testSetGameCycle()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setGameCycle(1);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::RYZOM_START_YEAR, (int)$this->ryzomClock->getRyzomYear());
    }

    public function testSetGameCycleWithDayOffset()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setGameCycle(1, false, null, 61);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::RYZOM_START_YEAR - 1, (int)$this->ryzomClock->getRyzomYear());
    }

    public function testSetGameCycleWithCustomYear()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $year = 2500;
        $this->ryzomClock->setGameCycle(1, false, null, null, $year);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals($year, (int)$this->ryzomClock->getRyzomYear());
    }

    public function testSetLegacyGameCycle() {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setLegacyGameCycle(1);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::LEGACY_RYZOM_START_YEAR - 1, (int)$this->ryzomClock->getRyzomYear());
    }

    /**
     * @param int   $tick
     * @param float $cycle
     * @param float $season
     * @param float $year
     * @param float $month
     * @param float $week
     * @param float $day
     * @param float $time
     */
    #[DataProvider('tickProvider')]
    public function testRyzomClock($tick, $startSpring, $startYear, $cycle, $season, $year, $month, $week, $day, $time)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $startSpring, $startYear);
        //printf("--- [%d] ---\n", $tick);
        //printf("cycle:%.5f\n", $this->ryzomClock->getRyzomCycle());
        //printf("season:%.5f\n", $this->ryzomClock->getRyzomSeason());
        //printf("year:%.5f\n", $this->ryzomClock->getRyzomYear());
        //printf("day:%.5f\n", $this->ryzomClock->getRyzomDay());
        //printf("month:%.5f\n", $this->ryzomClock->getRyzomMonth());
        //printf("week:%.5f\n", $this->ryzomClock->getRyzomWeek());
        //printf("time:%.5f\n", $this->ryzomClock->getRyzomTime());

        $this->assertEquals($cycle, floor($this->ryzomClock->getRyzomCycle()), 'invalid cycle');
        $this->assertEquals($season, floor($this->ryzomClock->getRyzomSeason()), 'invalid season');
        $this->assertEquals($year, floor($this->ryzomClock->getRyzomYear()), 'invalid year');
        $this->assertEquals($month, floor($this->ryzomClock->getRyzomMonth()), 'invalid month');
        $this->assertEquals($week, floor($this->ryzomClock->getRyzomWeek()), 'invalid week');
        $this->assertEquals($day, floor($this->ryzomClock->getRyzomDay()), 'invalid day');
        $this->assertEquals($time, floor($this->ryzomClock->getRyzomTime()), 'invalid time');
    }

    /**
     * @return array
     */
    static public function tickProvider()
    {
        // tick, startSpring, startYear, cycle, season, year, month, week, day, time
        return array(
            // with day offset
            array(0,       61, 2568, -1, -1, 2567, -3, -11, -61, 0),
            array(1800,    61, 2568, -1, -1, 2567, -3, -11, -61, 1), // +1hour
            array(43200,   61, 2568, -1, -1, 2567, -2, -10, -60, 0), // +1day
            array(2635200, 61, 2568,  0,  0, 2568,  0,   0,   0, 0), // +61days
            // no day offset
            array(0,       null, null, 0, 0, 2637, 0,  0,  0, 0),
            array(2635200, null, null, 0, 0, 2637, 2, 10, 61, 0), // +61days
            // Quinteth, Germinally 23, 1st AC 2637
            array(2265359, null, null, 0, 0, 2637, 1,  8, 52, 10),
        );
    }
}
