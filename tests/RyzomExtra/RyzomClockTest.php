<?php

namespace RyzomExtra;

use PHPUnit\Framework\Attributes\DataProvider;

class RyzomClockTest extends \PHPUnit\Framework\TestCase
{
    /** @var RyzomClock */
    protected $ryzomClock;

    public function setUp(): void
    {
        $this->ryzomClock = new RyzomClock(0, false);
    }

    public function testSetGameCycle()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setGameCycle(1);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::RYZOM_START_YEAR, (int) $this->ryzomClock->getRyzomYear());
    }

    public function testSetGameCycleWithDayOffset()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setGameCycle(1, false, null, 61);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::RYZOM_START_YEAR - 1, (int) $this->ryzomClock->getRyzomYear());
    }

    public function testSetGameCycleWithCustomYear()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $year = 2500;
        $this->ryzomClock->setGameCycle(1, false, null, null, $year);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals($year, (int) $this->ryzomClock->getRyzomYear());
    }

    public function testSetLegacyGameCycle()
    {
        $this->assertNotEquals(1, $this->ryzomClock->getGameCycle());

        $this->ryzomClock->setLegacyGameCycle(1);
        $this->assertEquals(1, $this->ryzomClock->getGameCycle());
        $this->assertEquals(RyzomClock::LEGACY_RYZOM_START_YEAR - 1, (int) $this->ryzomClock->getRyzomYear());
	}

    public function testSetGameCycleSync()
    {
        $this->ryzomClock->setGameCycle(1000, false, time() - 1);
        $this->assertEquals(1010, $this->ryzomClock->getGameCycle(), 'game tick must advance if sync is in the past');

        $this->ryzomClock->setGameCycle(1010, false, time() + 1);
        $this->assertEquals(1000, $this->ryzomClock->getGameCycle(), 'game tick must decrease if sync is in the future');
    }

    public function testGetSeasonFromRyzomDay()
    {
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(0));
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(89));
        $this->assertEquals(RyzomClock::SUMMER, $this->ryzomClock->getSeasonFromRyzomDay(90));
        $this->assertEquals(RyzomClock::SUMMER, $this->ryzomClock->getSeasonFromRyzomDay(179));
        $this->assertEquals(RyzomClock::AUTUMN, $this->ryzomClock->getSeasonFromRyzomDay(180));
        $this->assertEquals(RyzomClock::AUTUMN, $this->ryzomClock->getSeasonFromRyzomDay(269));
        $this->assertEquals(RyzomClock::WINTER, $this->ryzomClock->getSeasonFromRyzomDay(270));
        $this->assertEquals(RyzomClock::WINTER, $this->ryzomClock->getSeasonFromRyzomDay(359));
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(360));
    }

    public function testGetSeasonFromRyzomDayNegative()
    {
        $this->assertEquals(RyzomClock::WINTER, $this->ryzomClock->getSeasonFromRyzomDay(-1));
        $this->assertEquals(RyzomClock::AUTUMN, $this->ryzomClock->getSeasonFromRyzomDay(-91));
        $this->assertEquals(RyzomClock::AUTUMN, $this->ryzomClock->getSeasonFromRyzomDay(-180));
        $this->assertEquals(RyzomClock::SUMMER, $this->ryzomClock->getSeasonFromRyzomDay(-181));
        $this->assertEquals(RyzomClock::SUMMER, $this->ryzomClock->getSeasonFromRyzomDay(-270));
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(-271));
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(-360));
        $this->assertEquals(RyzomClock::WINTER, $this->ryzomClock->getSeasonFromRyzomDay(-361));
    }

    public function testLegacyGetSeasonFromRyzomDay()
    {
        $this->ryzomClock->setLegacyGameCycle(0);
        $this->assertEquals(RyzomClock::WINTER, $this->ryzomClock->getSeasonFromRyzomDay(-61));
        $this->assertEquals(RyzomClock::SPRING, $this->ryzomClock->getSeasonFromRyzomDay(0));
    }

    public function testVerifyGetCycle()
    {
        $this->ryzomClock->setGameCycle(0);
        $this->assertEqualsWithDelta(0, $this->ryzomClock->getRyzomCycle(), 0.0001);

        $this->ryzomClock->setLegacyGameCycle(0);
        $this->assertEqualsWithDelta(3.8305, $this->ryzomClock->getRyzomCycle(), 0.0001);

        $this->ryzomClock->setLegacyGameCycle(RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::LEGACY_RYZOM_SPRING_START);
        $this->assertEqualsWithDelta(0, $this->ryzomClock->getRyzomCycle(), 0.0001);

        // last cycle in year 0
        $this->ryzomClock->setGameCycle(RyzomClock::RYZOM_YEAR_IN_DAY * RyzomClock::RYZOM_DAY_IN_TICKS - RyzomClock::RYZOM_DAY_IN_TICKS);
        $this->assertEqualsWithDelta(3.9972, $this->ryzomClock->getRyzomCycle(), 0.0001);

        // next year cycle rollover to 0
        $this->ryzomClock->setGameCycle(RyzomClock::RYZOM_YEAR_IN_DAY * RyzomClock::RYZOM_DAY_IN_TICKS);
        $this->assertEquals(0, $this->ryzomClock->getRyzomCycle());
    }

    #[DataProvider('providerForGetMonth')]
    public function testRyzomClockGetMonth($tick, $springStart, $expected, $msg)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $springStart, 1000);
        $this->assertEqualsWithDelta($expected, $this->ryzomClock->getRyzomMonth(), 0.1);
    }

    /**
     * @return array
     *
     * @mago-format-ignore-next
     */
    public static function providerForGetMonth()
    {
        // tick, springStart, expected
        $data = [];
        foreach(range(0, RyzomClock::RYZOM_YEAR_IN_MONTH - 1) as $month) {
            if ($month > 0) {
                $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_MONTH_IN_DAY * $month - RyzomClock::RYZOM_DAY_IN_TICKS, 0, $month - 0.1, "month ".($month - 0.1)];
            }
            $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_MONTH_IN_DAY * $month, 0, $month, "month {$month}"];
            $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_MONTH_IN_DAY * $month + RyzomClock::RYZOM_DAY_IN_TICKS, 0, $month < RyzomClock::RYZOM_YEAR_IN_MONTH ? ($month + 0.1) : 0, $month < RyzomClock::RYZOM_YEAR_IN_MONTH ? "month ".($month + 0.1) : "month 0"];
        }
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_MONTH_IN_DAY * 48, 0, 0, "year 1, month 0"];
        // with day offset
        $data[] = [0, 61, 48 - 61 / 30, "year -1, last month, springStart = 61"];
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * 61, 61, 0, "year 0, month 0, springStart = 61"];
        return $data;
    }

    #[DataProvider('providerForGetWeek')]
    public function testRyzomClockGetWeek($tick, $springStart, $expected, $msg)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $springStart, 1000);
        $this->assertEqualsWithDelta($expected, $this->ryzomClock->getRyzomWeek(), 0.1);
    }

    /**
     * @return array
     *
     * @mago-format-ignore-next
     */
    public static function providerForGetWeek()
    {
        // tick, springStart, expected
        $data = [];
        $days = [
            0,1,2,3,4,5,6,7, // first week
            30,60,90,120,150,180,210,240,270,300,330,360, // first cycle
        ];
        foreach($days as $day) {
           $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS*$day, 0, $day / RyzomClock::RYZOM_WEEK_IN_DAY, "day {$day}"];
        }
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_YEAR_IN_DAY - RyzomClock::RYZOM_DAY_IN_TICKS, 0, 239.9, "last day of the year, day 1439"];
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_YEAR_IN_DAY, 0, 0, "year 1, day 0"];
        // with day offset
        $data[] = [0, 61, 240 - 61 / 6, "year -1, day -61, springStart = 61"];
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * 61, 61, 0, "year 0, day 0, springStart = 61"];
        return $data;
    }

    #[DataProvider('providerForGetSeason')]
    public function testRyzomClockGetSeason($tick, $springStart, $expected, $msg)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $springStart, 1000);
        $this->assertEqualsWithDelta($expected, $this->ryzomClock->getRyzomSeason(), 0.1);
    }

    /**
     * @return array
     *
     * @mago-format-ignore-next
     */
    public static function providerForGetSeason()
    {
        // tick, springStart, expected
        $data = [];
        $nbYearSeasons = RyzomClock::RYZOM_YEAR_IN_DAY / RyzomClock::RYZOM_SEASON_IN_DAY ;
        foreach(range(0, $nbYearSeasons- 1) as $season) {
            $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_SEASON_IN_DAY * $season, 0, $season, "season {$season}"];
        }
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * RyzomClock::RYZOM_YEAR_IN_DAY, 0, 0, "year 1, cycle 0, season 0"];
        // with day offset
        $data[] = [0, 61, 15.3, "year -1, last season, springStart = 61"];
        $data[] = [RyzomClock::RYZOM_DAY_IN_TICKS * 61, 61, 0, "year 0, day 0, season 0, springStart = 61"];
        return $data;
    }

    #[DataProvider('providerForGetDay')]
    public function testRyzomClockGetDay($tick, $springStart, $expected, $msg)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $springStart, 1000);
        $this->assertEqualsWithDelta($expected, $this->ryzomClock->getRyzomDays(), 0.1);
    }

    /**
     * @return array
     *
     * @mago-format-ignore-next
     */
    public static function providerForGetDay()
    {
        $dayTick = RyzomClock::RYZOM_DAY_IN_TICKS;

        // tick, springStart, expected
        $data = [];
        foreach([0, 100, RyzomClock::RYZOM_YEAR_IN_DAY - 1] as $day) {
            $data[] = [$dayTick * $day, 0, $day, "day {$day}"];
        }
        $data[] = [$dayTick * RyzomClock::RYZOM_YEAR_IN_DAY,            0, 0, "year 1, day 0"];
        $data[] = [$dayTick * RyzomClock::RYZOM_YEAR_IN_DAY + $dayTick, 0, 1, "year 1, day 1"];
        // with day offset
        $data[] = [0, 61, RyzomClock::RYZOM_YEAR_IN_DAY - 61, "year -1, day 1379, springStart = 61"];
        $data[] = [$dayTick * 61, 61, 0, "year 0, day 0, springStart = 61"];
        return $data;
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
    public function testRyzomClock($tick, $startSpring, $startYear, $cycle, $season, $year, $month, $week, $day, $time, $totalDays)
    {
        $this->ryzomClock->setGameCycle($tick, false, null, $startSpring, $startYear);

        $this->assertEquals($cycle, floor($this->ryzomClock->getRyzomCycle()), 'invalid cycle');
        $this->assertEquals($season, floor($this->ryzomClock->getRyzomSeason()), 'invalid season');
        $this->assertEquals($year, floor($this->ryzomClock->getRyzomYear()), 'invalid year');
        $this->assertEquals($month, floor($this->ryzomClock->getRyzomMonth()), 'invalid month');
        $this->assertEquals($week, floor($this->ryzomClock->getRyzomWeek()), 'invalid week');
        $this->assertEquals($day, floor($this->ryzomClock->getRyzomDays()), 'invalid day');
        $this->assertEquals($time, floor($this->ryzomClock->getRyzomTime()), 'invalid time');
        $this->assertEqualsWithDelta($totalDays, $this->ryzomClock->getRyzomDaysSinceSpring(), 0.1, 'invalid total days');
    }

    /**
     * @return array
     *
     * @mago-format-ignore-next
     */
    public static function tickProvider()
    {
        //        tick,      startSpring, startYear, cycle, season, year, month, week,  day, time, totalDays
        return array(
            // with day offset
            array(0,                  61,      2568,    3,      15, 2567,    45,  229, 1379,    0,        -61),
            array(1800,               61,      2568,    3,      15, 2567,    45,  229, 1379,    1,        -61), // +1hour
            array(43200,              61,      2568,    3,      15, 2567,    46,  230, 1380,    0,        -60), // +1day
            array(2635200,            61,      2568,    0,       0, 2568,     0,    0,    0,    0,          0), // +61days
            // no day offset
            array(0,                null,      null,     0,      0, 2637,     0,    0,    0,    0,          0),
            array(2635200,          null,      null,     0,      0, 2637,     2,   10,   60,    0,         61), // +61days
            // Quinteth, Germinally 23, 1st AC 2637
            array(2265359,          null,      null,     0,      0, 2637,     1,    8,   52,   10,         52.4),
            //
            array(99999999,         null,      null,     2,      9, 2638,    29,  145,  874,   19,       2314.8),
        );
    }
}
