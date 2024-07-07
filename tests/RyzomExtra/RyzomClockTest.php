<?php

namespace RyzomExtra;

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
	    $this->assertEquals(2567, (int)$this->ryzomClock->getRyzomYear());
    }

	public function testSetLegacyGameCycle() {
		$this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

		$this->ryzomClock->setLegacyGameCycle(1);
		$this->assertEquals(1, $this->ryzomClock->getGameCycle());
		$this->assertEquals(2524, (int)$this->ryzomClock->getRyzomYear());
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
     *
     * @dataProvider tickProvider
     */
    public function testRyzomClock($tick, $cycle, $season, $year, $month, $week, $day, $time)
    {
        $this->ryzomClock->setGameCycle($tick);
        //printf("--- [%d] ---\n", $tick);
        //printf("cycle:%.5f\n", $this->ryzomClock->getRyzomCycle());
        //printf("season:%.5f\n", $this->ryzomClock->getRyzomSeason());
        //printf("year:%.5f\n", $this->ryzomClock->getRyzomYear());
        //printf("day:%.5f\n", $this->ryzomClock->getRyzomDay());
        //printf("month:%.5f\n", $this->ryzomClock->getRyzomMonth());
        //printf("week:%.5f\n", $this->ryzomClock->getRyzomWeek());
        //printf("time:%.5f\n", $this->ryzomClock->getRyzomTime());

        $this->assertEquals($cycle, floor($this->ryzomClock->getRyzomCycle()));
        $this->assertEquals($season, floor($this->ryzomClock->getRyzomSeason()));
        $this->assertEquals($year, floor($this->ryzomClock->getRyzomYear()));
        $this->assertEquals($month, floor($this->ryzomClock->getRyzomMonth()));
        $this->assertEquals($week, floor($this->ryzomClock->getRyzomWeek()));
        $this->assertEquals($day, floor($this->ryzomClock->getRyzomDay()));
        $this->assertEquals($time, floor($this->ryzomClock->getRyzomTime()));
    }

    /**
     * @return array
     */
    static public function tickProvider()
    {
        // tick, cycle, season, year, month, week, day, time
        return array(
            array(0, -1, -1, 2567, -3, -11, -61, 0),
            // +1hour
            array(1800, -1, -1, 2567, -3, -11, -61, 1),
            // +1day
            array(43200, -1, -1, 2567, -2, -10, -60, 0),
            // +61days
            array(2635200, 0, 0, 2568, 0, 0, 0, 0),

        );
    }
}
