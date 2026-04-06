<?php

//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2013 Meelis Mägi <nimetu@gmail.com>
//
// This file is part of RyzomExtra.
//
// RyzomExtra is free software; you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation; either version 3 of the License, or
// (at your option) any later version.
//
// RyzomExtra is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program; if not, write to the Free Software Foundation,
// Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
//

namespace RyzomExtra;

/**
 * Class RyzomClock
 */
class RyzomClock
{
    /** ingame ticks in realtime seconds, 1tick = 100ms */
    const RYZOM_TICK_IN_SECOND = 10;

    /** <game_share/time_weather_season/time_and_season.h> */
    /** ticks in hour - 10 * 3 * 60 */
    const RYZOM_HOURS_IN_TICKS = 1800;
    /** hours in day */
    const RYZOM_DAY_IN_HOUR = 24;
    /** ticks in day - HOURS_IN_TICKS * DAY_IN_HOUR */
    const RYZOM_DAY_IN_TICKS = 43200;
    /** days in season */
    const RYZOM_SEASON_IN_DAY = 90;
    /** days in month */
    const RYZOM_MONTH_IN_DAY = 30;
    /** months in year */
    const RYZOM_YEAR_IN_MONTH = 48;
    /** days in week */
    const RYZOM_WEEK_IN_DAY = 6;
    /** days in year - MONTH_IN_DAY * YEAR_IN_MONTH */
    const RYZOM_YEAR_IN_DAY = 1440;
    /** months in cycle */
    const RYZOM_CYCLE_IN_MONTH = 12;
    /** day offset (not used, c++ repo commit 476bffdd */
    const RYZOM_START_SPRING = 0;
    /** starting year */
    const RYZOM_START_YEAR = 2637;
    /** start year for game cycles from old shards */
    const LEGACY_RYZOM_START_YEAR = 2525;
    const LEGACY_RYZOM_SPRING_START = 61;

    /** number of seasons in cycle */
    const RYZOM_NB_SEASONS = 4;

    /** seasons values */
    const SPRING = 0;
    const SUMMER = 1;
    const AUTUMN = 2;
    const WINTER = 3;

    /** @var int */
    protected $gameCycle;

    /** @var bool */
    protected $legacy;

    /**
     * Days from $startSpring
     *
     * @var float
     */
    protected $daysSinceSpring;

    /**
     * Current ryzom year
     *
     * @var float
     */
    protected $ryzomYear;

    /**
     * Day within current cycle
     *
     * @var float
     */
    protected $ryzomDay;

    /**
     * Hour within current day
     *
     * @var float
     **/
    protected $ryzomTime;

    /** @var int */
    protected $startSpring;

    /** @var int */
    protected $startYear;

    /**
     * @param int  $tick
     * @param bool $legacy
     * @param int|null $sync
     */
    public function __construct($tick, $legacy = false, $sync = null)
    {
        $this->setGameCycle($tick, $legacy, $sync);
    }

    /**
     * Return time in 0..24 range
     *
     * @return float
     */
    public function getRyzomTime()
    {
        return $this->ryzomTime;
    }

    /**
     * @return float
     *
     * @deprecated use getRyzomDaysSinceSpring()
     */
    public function getRyzomDay()
    {
        return $this->getRyzomDaysSinceSpring();
    }

    /**
     * Return day in current year 0.0 >= day < 1440.0 range, ie 1439.9 = last cycle, last season, ~last day
     *
     * @return float
     */
    public function getRyzomDays()
    {
        return $this->ryzomDay;
    }

    /**
     * Return total days starting from springStart day.
     *
     * If springStart > 0 then this returns negative value.
     *
     * @return float
     */
    public function getRyzomDaysSinceSpring()
    {
        return $this->daysSinceSpring;
    }

    /**
     * Returns current full year for shard
     *
     * @return float
     */
    public function getRyzomYear()
    {
        return $this->ryzomYear;
    }

    /**
     * Return week in current year 0.0 >= week < 240.0 range, ie 239.9 = last cycle, last month, last week, ~last day
     *
     * @return float
     */
    public function getRyzomWeek()
    {
        $year = $this->getRyzomYear();
        $day = ($year - floor($year)) * self::RYZOM_YEAR_IN_DAY;
        return $day / self::RYZOM_WEEK_IN_DAY;
    }

    /**
     * Return season in current year 0.0 >= season < 16.0 range
     *
     * @return float
     */
    public function getRyzomSeason()
    {
        $year = $this->getRyzomYear();
        $day = ($year - floor($year)) * self::RYZOM_YEAR_IN_DAY;
        return $day / self::RYZOM_SEASON_IN_DAY;
    }

    /**
     * Return season index in 0..3 range from ryzom (total) day index
     *
     * NOTE: day should be positive as game code does not support negative days.
     *
     * Day   0 is spring
     * Day -61 is winter (legacy game tick start)
     *
     * @param float $day day as positive value, negative values will not be compatible with ingame
     *
     * @return int
     */
    public static function getSeasonFromRyzomDay($day)
    {
        if ($day < 0) {
            $days = self::RYZOM_NB_SEASONS * self::RYZOM_SEASON_IN_DAY;
            $day = $days - fmod(abs($day), $days);
        }
        return (int) fmod(fmod($day, self::RYZOM_YEAR_IN_DAY) / self::RYZOM_SEASON_IN_DAY, self::RYZOM_NB_SEASONS);
    }

    /**
     * Return current month within cycle 0.0 >= month < 48.0 range, ie 29.9 = 1st cycle, 1st season, 1st month, ~last day
     *
     * @return float
     */
    public function getRyzomMonth()
    {
        $year = $this->getRyzomYear();
        $day = ($year - floor($year)) * self::RYZOM_YEAR_IN_DAY;
        return $day / self::RYZOM_MONTH_IN_DAY;
    }

    /**
     * Return current cycle in 0.0 >= cycle < 4.0 range, ie 3.5
     *
     * @return float
     */
    public function getRyzomCycle()
    {
        return $this->getRyzomMonth() / self::RYZOM_CYCLE_IN_MONTH;
    }

    /**
     * @return int
     */
    public function getGameCycle()
    {
        return $this->gameCycle;
    }

    /**
     * Set custom game cycle for clock
     *
     * @param int  $gameCycle
     * @param bool $legacy
     * @param int|null $sync UTC timestamp when gameCycle was taken
     * @param int|null $startSpring day offset for first spring, set to 61 for older (2026-01-04 rollover) ticks
     * @param int|null $startYear starting year if legaycy=false, for older (2026-01-04 rollover) ticks set to 2568
     */
    public function setGameCycle($gameCycle, $legacy = false, $sync = null, $startSpring = null, $startYear = null)
    {
        if ($sync !== null) {
            $gameCycle += (time() - $sync) * self::RYZOM_TICK_IN_SECOND;
        }
        $this->gameCycle = $gameCycle;
        $this->legacy = $legacy;
        $this->startSpring = $startSpring !== null ? $startSpring : self::RYZOM_START_SPRING;

        if ($startYear !== null) {
            $this->startYear = $startYear;
        } elseif ($legacy) {
            $this->startYear = self::LEGACY_RYZOM_START_YEAR;
        } else {
            $this->startYear = self::RYZOM_START_YEAR;
        }

        // ingame days and hours
        $hours = $this->gameCycle / self::RYZOM_HOURS_IN_TICKS;
        $this->daysSinceSpring = ($this->gameCycle / self::RYZOM_DAY_IN_TICKS) - $this->startSpring;

        //
        $this->ryzomYear = ($this->daysSinceSpring / self::RYZOM_YEAR_IN_DAY) + $this->startYear;
        $this->ryzomDay = ($this->ryzomYear - floor($this->ryzomYear)) * RyzomClock::RYZOM_YEAR_IN_DAY;
        $this->ryzomTime = fmod($hours, self::RYZOM_DAY_IN_HOUR);
    }

    /**
     * Set tick for legacy shards with correct spring start offset
     *
     * @param int $gameCycle
     * @param int|null $sync
     * @param int $startSpring
     */
    public function setLegacyGameCycle($gameCycle, $sync = null, $startSpring = self::LEGACY_RYZOM_SPRING_START)
    {
        $this->setGameCycle($gameCycle, true, $sync, $startSpring);
    }

    /**
     * Return starting year for shard
     *
     * @return int
     */
    protected function getShardStartYear()
    {
        return $this->startYear;
    }
}
