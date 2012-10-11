<?php

namespace RyzomExtra;

use Ryzom\Misc\BitStruct;

/**
 * GuildIconBuilder
 * <code/ryzom/client/src/interface_v3/guild_manager.h>
 *
 * @property int $Background
 * @property int $Symbol
 * @property int $Inverted
 * @property int $Color1Red
 * @property int $Color1Green
 * @property int $Color1Blue
 * @property int $Color2Red
 * @property int $Color2Green
 * @property int $Color2Blue
 */
class GuildIconBuilder extends BitStruct
{
    /**
     * GuildIcon
     *
     * Background and Symbol are encoded value+1
     * which makes '17' as first legal composite value
     *
     * @param string $icon
     */
    public function __construct($icon = '17')
    {
        parent::__construct(array(
            'Background' => 4,
            'Symbol' => 6,
            'Inverted' => 1,
            'Color1Red' => 8,
            'Color1Green' => 8,
            'Color1Blue' => 8,
            'Color2Red' => 8,
            'Color2Green' => 8,
            'Color2Blue' => 8,
        ));
        // $icon is 64bit, so treat it as string
        if (strlen($icon) < 3 && intval($icon) < 17) {
            $icon = 17;
        }
        $this->setValue($icon);
    }

    /**
     * Background and Symbol are coded as value+1
     *
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $result = parent::__get($name);
        if ($name == 'Background' || $name == 'Symbol') {
            $result = max(0, $result - 1);
        }
        return $result;
    }

    /**
     * Encode Background and Symbol as value+1,
     *
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        if ($name == 'Background' || $name == 'Symbol') {
            $value++;
        }
        parent::__set($name, $value);
    }

}
