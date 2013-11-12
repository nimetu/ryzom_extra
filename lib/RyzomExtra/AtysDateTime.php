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
 * Class to convert shard game cycle to atys date
 */
class AtysDateTime extends RyzomClock
{

    /**
     * Season translation
     *
     * @var array
     */
    protected $seasonNames = array(
        'en' => array('Spring', 'Summer', 'Autumn', 'Winter'),
        'fr' => array('au Printemps', 'en Eté', 'en Automne', 'en Hiver'),
        'de' => array('Frühling', 'Sommer', 'Herbst', 'Winter'),
        'ru' => array('весна', 'лето', 'осень', 'зима'),
        'es' => array('Primavera', 'Verano', 'Otoño', 'Invierto'),
    );
    /** @var string[] */
    protected $monthNames = array(
        'Winderly',
        'Germinally',
        'Folially',
        'Floris',
        'Medis',
        'Thermis',
        'Harvestor',
        'Frutor',
        'Fallenor',
        'Pluvia',
        'Mystia',
        'Nivia'
    );
    /** @var string[] */
    protected $dayNames = array(
        'Prima',
        'Dua',
        'Tria',
        'Quarta',
        'Quinteth',
        'Holeth'
    );
    /** @var string[] */
    protected $uiJenasYear = array(
        'en' => 'JY',
        'fr' => 'AJ',
        'de' => 'JJ',
        'ru' => 'ГД',
        'es' => 'JY',
    );
    /** @var string[] */
    protected $uiAtysCycle = array(
        'en' => 'AC',
        'fr' => 'CA',
        'de' => 'AZ',
        'ru' => 'ЦА',
        'es' => 'AC',
    );
    /**
     * Translation for 1st, 2nd, 3rd, 4th (hopefully correctly)
     * array needs at least 5 elements
     *
     * @var array
     */
    protected $uiTh = array(
        'en' => array(0 => '', 1 => 'st', 2 => 'nd', 3 => 'rd', 4 => 'th'),
        'fr' => array(0 => '', 1 => 'ère', 2 => 'ème', 3 => 'ème', 4 => 'ème',),
        'de' => array(0 => '', 1 => '.', 2 => '.', 3 => '.', 4 => '.'),
        'ru' => array(0 => '', 1 => '', 2 => '', 3 => '', 4 => ''),
        'es' => array(0 => '', 1 => '', 2 => '', 3 => '', 4 => ''),
    );
    /** @var string */
    protected $lang;

    /**
     * @param string $lang
     * @param bool   $legacyTick  If set to true, tick start from year 2525 (aniro, arispotle, leanon)
     *                            else tick start from 2568 (atys)
     *
     * @throw \RuntimeException
     */
    public function __construct($lang = 'en', $legacyTick = false)
    {
        parent::__construct(0, 0, $legacyTick);
        $this->lang = $lang;
    }

    /**
     * Return month name in given season
     *
     * @param int $season Season index in 1..4 range
     * @param int $month  Month index in 1..3 range
     *
     * @return string
     */
    public function getSeasonMonthName($season = null, $month = null)
    {
        if ($season === null) {
            $season = $this->getSeason();
        }
        if ($month === null) {
            $month = $this->getMonth();
        }
        if ($month <= 0) {
            $month += count($this->monthNames);
        }
        $idx = ($season - 1) * 3 + ($month - 1);

        return $this->monthNames[$idx];
    }

    /**
     * Return season name by index and language
     * 1 = Spring, 2 = Summer,  3 = Autumn, 4 = Winter
     *
     * @param int    $index season index in 1..4 range
     * @param string $lang language code like 'en', 'fr', etc
     *
     * @return string
     */
    public function getSeasonName($index = null, $lang = null)
    {
        $key = ($lang ? : $this->lang);

        // fall back to first language available
        if (!isset($this->seasonNames[$key])) {
            $key = key($this->seasonNames);
        }
        if ($index === null) {
            $index = $this->getSeason();
        }
        if ($index <= 0) {
            $index += count($this->seasonNames[$key]);
        }
        $idx = $index - 1;

        return $this->seasonNames[$key][$idx];
    }

    /**
     * Return Ryzom season
     * 1 = Spring, 2 = Summer,  3 = Autumn, 4 = Winter
     *
     * @param bool $asInt
     *
     * @return int
     */
    public function getSeason($asInt = true)
    {
        $value = fmod($this->getRyzomSeason(), 4) + 1;

        return $asInt ? (int) $value : $value;
    }

    /**
     * @param bool $asInt
     *
     * @return int|float
     */
    public function getWeek($asInt = true)
    {
        $value = fmod($this->getRyzomWeek(), 5) + 1;

        return $asInt ? (int) $value : $value;
    }

    /**
     * Tries to parse atys date string and return tick for it
     *
     * @param string $atysDate
     *
     * @throws \RuntimeException
     */
    public function parse($atysDate)
    {
        throw new \RuntimeException("Not implemented");
    }

    /**
     * Set language
     *
     * @param string $lang
     */
    public function setLanguage($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Return atys date in default ingame format
     *
     * alias for __toString()
     *
     * @return string
     */
    public function toDateString()
    {
        return $this->__toString();
    }

    /**
     * Return atys date in string format
     *
     * @return string
     */
    public function __toString()
    {
        return $this->formatDate(false, false);
    }

    /**
     * Convert shard tick to readable atys date using currently set language
     *
     * @param bool $show_hour if set to true, include hour. if $show_min is false, then also append 'h'
     * @param bool $show_min  if set to true, include minutes. only used if $show_hour is also true
     *
     * @return string
     */
    public function formatDate($show_hour = false, $show_min = false)
    {
        // Tria, Thermis 07, 4th AC 2546
        $ig = '%s, %s %02d, %d%s %s %d';
        $str = sprintf(
            $ig,
            $this->getDayName(),
            $this->getMonthName(),
            $this->getDate(),
            $this->getCycle(),
            $this->translateTh($this->getCycle()),
            $this->translateAtysCycle(),
            $this->getYear()
        );
        if ($show_hour) {
            $str = $this->toTimeString($show_min) . ' - ' . $str;
        }

        return $str;
    }

    /**
     * Return day name
     *
     * @param int $day
     *
     * @return string
     */
    public function getDayName($day = null)
    {
        if ($day === null) {
            $day = $this->getDay();
        }
        if ($day <= 0) {
            $day += count($this->dayNames);
        }

        $idx = $day - 1;

        return $this->dayNames[$idx];
    }

    /**
     * Returns day of week in the 1 to 6 range
     * Day of month is returned by getDate() method
     *
     * @param bool $asInt
     *
     * @return int|float
     */
    public function getDay($asInt = true)
    {
        $value = fmod($this->getRyzomDay(), self::RYZOM_WEEK_IN_DAY) + 1;

        return $asInt ? (int) $value : $value;
    }

    /**
     * @param bool $asInt
     *
     * @return float|int
     */
    public function getDayOfSeason($asInt = true)
    {
        $value = fmod($this->getRyzomDay(), self::RYZOM_SEASON_IN_DAY) + 1;

        return $asInt ? (int) $value : $value;
    }

    /**
     * Return month name
     *
     * @param int $month Month index in 1..12 range
     *
     * @return string
     */
    public function getMonthName($month = null)
    {
        if ($month === null) {
            $month = $this->getMonth();
        }
        if ($month <= 0) {
            $month += count($this->monthNames);
        }

        $idx = $month - 1;

        return $this->monthNames[$idx];
    }

    /**
     * Return month in the 1..12 range
     *
     * @param bool $asInt
     *
     * @return int|float
     */
    public function getMonth($asInt = true)
    {
        $value = fmod($this->getRyzomMonth(), self::RYZOM_CYCLE_IN_MONTH) + 1;

        return $asInt ? (int) $value : $value;
    }

    /**
     * Return Day of Month in the 1 to 30 range
     *
     * @param bool $asInt
     *
     * @return int|float
     */
    public function getDate($asInt = true)
    {
        $result = fmod($this->getRyzomDay(), self::RYZOM_MONTH_IN_DAY) + 1;

        return $asInt ? (int) $result : $result;
    }

    /**
     * @param bool $asInt
     *
     * @return int|float
     */
    public function getCycle($asInt = true)
    {
        $result = ($this->getRyzomMonth() / self::RYZOM_CYCLE_IN_MONTH) + 1;

        return $asInt ? (int) $result : $result;
    }

    /**
     * Same as getRyzomYear(), but always return int
     *
     * @return int
     */
    public function getYear()
    {
        return (int) $this->getRyzomYear();
    }

    /**
     * Returns atys time. in ##h format or HH:MM format is $show_min is set to TRUE
     *
     * @param bool $show_min if set to TRUE, then also include minutes
     *
     * @return string
     */
    public function toTimeString($show_min = false)
    {
        if ($show_min !== true) {
            return sprintf("%02d", $this->getHours()) . 'h';
        } else {
            return sprintf("%02d:%02d", $this->getHours(), $this->getMinutes());
        }
    }

    /**
     * Returns atys hour in the 0 to 59 range
     *
     * @return int
     */
    public function getHours()
    {
        return intval($this->getRyzomTime());
    }

    /**
     * Return atys minute in the 0 to 59 range
     *
     * @return int
     */
    public function getMinutes()
    {
        return intval(($this->getRyzomTime() - $this->getHours()) * 60);
    }

    /**
     * Returns translated 'JY' string
     *
     * @return string
     */
    protected function translateJenasYear()
    {
        $key = $this->lang;
        // fall back to first translation available
        if (!isset($this->uiJenasYear[$key])) {
            $key = key($this->uiJenasYear);
        }

        return $this->uiJenasYear[$key];
    }

    /**
     * Returns '1st', '2nd', '3rd', '4th' string part for given number
     *
     * @param $nr
     *
     * @return string
     */
    protected function translateTh($nr)
    {
        // fr: 1ère, 2ème, 3ème, 4ème
        // de: 1., 2., 3., 4.
        $key = $this->lang;
        if (!isset($this->uiTh[$key])) {
            $key = key($this->uiTh);
        }
        if (isset($this->uiTh[$key][$nr])) {
            return $this->uiTh[$key][$nr];
        } else {
            return $this->uiTh[$key][4];
        }
    }

    /**
     * Returns translated 'AC' string
     *
     * @return string
     */
    protected function translateAtysCycle()
    {
        $key = $this->lang;
        // fall back to first translation available
        if (!isset($this->uiAtysCycle[$key])) {
            $key = key($this->uiAtysCycle);
        }

        return $this->uiAtysCycle[$key];
    }

}
