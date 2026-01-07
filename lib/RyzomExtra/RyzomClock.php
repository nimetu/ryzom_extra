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

    /** number of seasons in cycle */
    const RYZOM_NB_SEASONS = 4;

    /** @var int */
    protected $gameCycle;

    /** @var bool */
    protected $legacy;

    /** @var float */
    protected $ryzomDay;

    /** @var float */
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
     * @return float
     */
    public function getRyzomTime()
    {
        return $this->ryzomTime;
    }

    /**
     * @return float
     */
    public function getRyzomDay()
    {
        return $this->ryzomDay;
    }

    /**
     * Returns current full year for shard
     *
     * @return float
     */
    public function getRyzomYear()
    {
        $result = $this->getRyzomDay() / self::RYZOM_YEAR_IN_DAY + $this->getShardStartYear();

        return $result;
    }

    /**
     * @return float
     */
    public function getRyzomWeek()
    {
        $result = fmod($this->getRyzomDay(), self::RYZOM_YEAR_IN_DAY) / self::RYZOM_WEEK_IN_DAY;

        return $result;
    }

    /**
     * @return float
     */
    public function getRyzomSeason()
    {
        $result = fmod($this->getRyzomDay(), self::RYZOM_YEAR_IN_DAY) / self::RYZOM_SEASON_IN_DAY;

        return $result;
    }

    /**
     * Return season index in 0..3 range from ryzom (total) day index
     *
     * @param float $day
     *
     * @return int
     */
    public static function getSeasonFromRyzomDay($day)
    {
        return (int) fmod(fmod($day, self::RYZOM_YEAR_IN_DAY) / self::RYZOM_SEASON_IN_DAY, self::RYZOM_NB_SEASONS);
    }

    /**
     * @return float
     */
    public function getRyzomMonth()
    {
        $result = fmod($this->getRyzomDay(), self::RYZOM_YEAR_IN_DAY) / self::RYZOM_MONTH_IN_DAY;

        return $result;
    }

    /**
     * @return float
     */
    public function getRyzomCycle()
    {
        $result = $this->getRyzomMonth() / self::RYZOM_CYCLE_IN_MONTH;

        return $result;
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
     * @param int $startSpring day offset for first spring, set to 61 for older (2026-01-04 rollover) ticks
     * @param int $startYear starting year if legaycy=false, for older (2026-01-04 rollover) ticks set to 2568
     */
    public function setGameCycle($gameCycle, $legacy = false, $sync = null, $startSpring = null, $startYear = null)
    {
        if ($sync !== null) {
            $gameCycle += (time() - $sync) * 10;
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
        $this->ryzomDay = ($hours / 24) - $this->startSpring;
        $this->ryzomTime = fmod($hours, 24);
    }

    /**
     * Set tick for legacy shards with correct spring start offset
     *
     * @param int $gameCycle
     * @param int|null $sync
     */
    public function setLegacyGameCycle($gameCycle, $sync = null, $startSpring = 61)
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
