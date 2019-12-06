<?php
//
// RyzomExtra - https://github.com/nimetu/ryzom_extra
// Copyright (c) 2012 Meelis Mägi <nimetu@gmail.com>
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

/**
 * NOTE: this file uses a lot of memory. One language + item + resource stats is around 75MiB of memory
 *       might be best to export data sets to database if php memory is limited
 */
//error_reporting(E_ALL);

// patch where to find data sets
define('RYZOM_EXTRA_PATH', dirname(__FILE__) . '/resources');
define('RYZOM_EXTRA_SHEETS_CACHE', RYZOM_EXTRA_PATH . '/sheets-cache');

class RyzomExtra
{
    // Record type - ['type']
    // <code/ryzom/common/game_share/item_family.h>
    // undefined = 0
    // service = 1
    const TYPE_ARMOR = 2;
    const TYPE_MELEE = 3;
    const TYPE_RANGE = 4;
    const TYPE_AMMO = 5;
    const TYPE_RESOURCE = 6;
    const TYPE_SHIELD = 7;
    const TYPE_TOOLS = 8; // crafting
    const TYPE_PICK = 9; // harvest
    // taming tool = 10
    // training tool = 11
    // ai = 12
    // brick = 13
    // food = 14
    const TYPE_JEWEL = 15;
    // corpse = 16
    // carrion = 17
    // bag = 18
    // stack = 19
    // dead seed = 20
    const TYPE_TPTICKET = 21;
    // guild flag = 22
    // living seed = 23
    // little seed = 24
    // medium seed = 25
    // big seed = 26
    // very big seed = 27
    // mission item = 28
    // crystalized spell = 29
    // item sap recharge = 30
    // pet animal ticket = 31
    // guild option = 32
    // handled item = 33
    // cosmetic = 34
    const TYPE_CONSUMABLE = 35;
    const TYPE_XPCAT = 36;
    // scroll = 37
    // scroll r2 = 38
    // command ticket = 39
    // generic item = 40
    const TYPE_CASINO = 40;
    //
    // Item type - ['item_type']
    // <code/ryzom/common/game_share/item_type.h>
    const ITEM_DAGGER = 0;
    const ITEM_SWORD = 1;
    const ITEM_MACE = 2;
    const ITEM_AXE = 3;
    const ITEM_SPEAR = 4;
    const ITEM_STAFF = 5;
    //
    const ITEM_2H_SWORD = 6;
    const ITEM_2H_AXE = 7;
    const ITEM_PIKE = 8;
    const ITEM_2H_MACE = 9;
    //
    const ITEM_AUTOLAUNCHER = 10;
    const ITEM_BOWRIFLE = 11;
    const ITEM_LAUNCHER = 12;
    const ITEM_PISTOL = 13;
    const ITEM_BOWPISTOL = 14;
    const ITEM_RIFLE = 15;
    //
    const ITEM_AUTOLAUNCHER_AMMO = 16;
    const ITEM_BOWRIFLE_AMMO = 17;
    const ITEM_LAUNCHER_AMMO = 18;
    const ITEM_PISTOL_AMMO = 19;
    const ITEM_BOWPISTOL_AMMO = 20;
    const ITEM_RIFLE_AMMO = 21;
    //
    const ITEM_SHIELD = 22;
    const ITEM_BUCKLER = 23;
    //
    const ITEM_LA_BOOTS = 24;
    const ITEM_LA_GLOVES = 25;
    const ITEM_LA_PANTS = 26;
    const ITEM_LA_SLEEVES = 27;
    const ITEM_LA_VEST = 28;
    //
    const ITEM_MA_BOOTS = 29;
    const ITEM_MA_GLOVES = 30;
    const ITEM_MA_PANTS = 31;
    const ITEM_MA_SLEEVES = 32;
    const ITEM_MA_VEST = 33;
    //
    const ITEM_HA_BOOTS = 34;
    const ITEM_HA_GLOVES = 35;
    const ITEM_HA_PANTS = 36;
    const ITEM_HA_SLEEVES = 37;
    const ITEM_HA_VEST = 38;
    const ITEM_HA_HELMET = 39;
    //
    const ITEM_ANKLET = 40;
    const ITEM_BRACELET = 41;
    const ITEM_DIADEM = 42;
    const ITEM_EARRING = 43;
    const ITEM_PENDANT = 44;
    const ITEM_RING = 45;
    //
    const ITEM_PICK = 46;
    const ITEM_ARMOR_CTOOL = 47;
    const ITEM_AMMO_CTOOL = 48;
    const ITEM_MELEE_CTOOL = 49;
    const ITEM_RANGE_CTOOL = 50;
    const ITEM_JEWEL_CTOOL = 51;
    const ITEM_TOOL_CTOOL = 52;
    // 53 - campsfire
    const ITEM_MEKTOUB_PACKER = 54;
    const ITEM_MEKTOUB_MOUNT = 55;
    const ITEM_ANIMAL_TICKET = 56;
    const ITEM_FORAGE_BALE = 57;
    const ITEM_MAGIC_AMPLIFIER = 58;
    // 59 - hom hairstyle
    // 60 - hom hair color
    // 61 - hom tatoo
    // 62 - hof hairstyle
    // 63 - hof hair color
    // 64 - hof tatoo
    // 65 - service stable
    // 66 - job element
    const ITEM_GENERIC = 67; // sap recharge, casino ticker/token/title
    const ITEM_UNDEFINED = 68;
    //
    // @deprecated should not be used, probably removed in next major version
    const ITEM_2h_AXE = 7;// use ITEM_2H_AXE instead
    const ITEM_T_65 = 67; // sap recharge, casino ticker/token/title
    const ITEM_OTHER = 68; // generic (mats)
    //
    // weapon/ammo damage
    const DMG_SLASH = 0;
    const DMG_PIERCE = 1;
    const DMG_SMASH = 2;

    //
    static function uxt_damage($dmg)
    {
        switch ($dmg) {
            case self::DMG_SLASH  :
                return 'dtSLASHING';
            case self::DMG_PIERCE :
                return 'dtPIERCING';
            case self::DMG_SMASH  :
                return 'dtBLUNT';
        }
        return 'NotExist:dmg #' . $dmg;
    }
    //
    // bitfield: craft resource type - resource usually has 2 or more bits set
    // CODE: if((mpft & (1<<MPFT_BLADE)) != 0) // matches blade (shell, wondermats, kitin larva, ..)
    // SQL: WHERE (MPFT & (1<<MPFT_BLADE))<>0
    const MPFT_BLADE = 0;
    const MPFT_HAMMER = 1;
    const MPFT_POINT = 2;
    const MPFT_SHAFT = 3;
    const MPFT_GRIP = 4;
    const MPFT_COUNTERWEIGHT = 5;
    const MPFT_TRIGGER = 6;
    const MPFT_FIRING_PIN = 7;
    const MPFT_BARREL = 8;
    const MPFT_EXPLOSIVE = 9;
    const MPFT_AMMO_JACKET = 10;
    const MPFT_AMMO_BULLET = 11;
    const MPFT_ARMOR_SHELL = 12;
    const MPFT_LINING = 13;
    const MPFT_STUFFING = 14;
    const MPFT_ARMOR_CLIP = 15;
    const MPFT_JEWEL_SETTING = 16;
    const MPFT_JEWEL = 17;
    const MPFT_BLACKSMITH_TOOL = 18;
    const MPFT_PESTLE_TOOL = 19;
    const MPFT_SHARPENER_TOOL = 20;
    const MPFT_TUNNELING_KNIFE = 21;
    const MPFT_JEWELRY_HAMMER = 22;
    const MPFT_CAMPFIRE = 23;
    const MPFT_CLOTHES = 24;
    const MPFT_MAGIC_FOCUS = 25;
    const MPFT_UNKNOWN = 26;
    //
    static $mpft_to_bit = array(
        'mpftMpL' => self::MPFT_BLADE,
        'mpftMpH' => self::MPFT_HAMMER,
        'mpftMpP' => self::MPFT_POINT,
        'mpftMpM' => self::MPFT_SHAFT,
        'mpftMpG' => self::MPFT_GRIP,
        'mpftMpC' => self::MPFT_COUNTERWEIGHT,
        'mpftMpGA' => self::MPFT_TRIGGER,
        'mpftMpPE' => self::MPFT_FIRING_PIN,
        'mpftMpCA' => self::MPFT_BARREL,
        'mpftMpE' => self::MPFT_EXPLOSIVE,
        'mpftMpEN' => self::MPFT_AMMO_JACKET,
        'mpftMpPR' => self::MPFT_AMMO_BULLET,
        'mpftMpCR' => self::MPFT_ARMOR_SHELL,
        'mpftMpRI' => self::MPFT_LINING,
        'mpftMpRE' => self::MPFT_STUFFING,
        'mpftMpAT' => self::MPFT_ARMOR_CLIP,
        'mpftMpSU' => self::MPFT_JEWEL_SETTING,
        'mpftMpED' => self::MPFT_JEWEL,
        'mpftMpBT' => self::MPFT_BLACKSMITH_TOOL,
        'mpftMpPES' => self::MPFT_PESTLE_TOOL,
        'mpftMpSH' => self::MPFT_SHARPENER_TOOL,
        'mpftMpTK' => self::MPFT_TUNNELING_KNIFE,
        'mpftMpJH' => self::MPFT_JEWELRY_HAMMER,
        'mpftMpCF' => self::MPFT_CAMPFIRE,
        'mpftMpVE' => self::MPFT_CLOTHES,
        'mpftMpMF' => self::MPFT_MAGIC_FOCUS,
        'mpft' => self::MPFT_UNKNOWN, // [Undefined Raw Material Target]
    );

    // turn mpft bit back to uxt id that can be used in translation
    static function uxt_mpft($val)
    {
        foreach (self::$mpft_to_bit as $uxt => $bit) {
            if ($val == $bit) {
                return $uxt;
            }
        }
        return 'NotExist:bit #' . $val;
    }

    // these make up uxt translation id like 'mpstat0' == durability or 'mpstatItemQualifier0' = 'of Durability'
    static $stat_to_int = array(
        'durability' => 0,
        'lightness' => 1,
        'sap_load' => 2,
        'dmg' => 3,
        'speed' => 4,
        'range' => 5,
        'dodge_modifier' => 6,
        'parry_modifier' => 7,
        'adversary_dodge_modifier' => 8,
        'adversary_parry_modifier' => 9,
        'protection_factor' => 10,
        'max_slashing_protection' => 11,
        'max_smashing_protection' => 12,
        'max_piercing_protection' => 13,
        'acid_protection' => 14,
        'cold_protection' => 15,
        'rot_protection' => 16,
        'fire_protection' => 17,
        'shockwave_protection' => 18,
        'poison_protection' => 19,
        'electric_protection' => 20,
        'desert_resistance' => 21,
        'forest_resistance' => 22,
        'lake_resistance' => 23,
        'jungle_resistance' => 24,
        'prime_roots_resistance' => 25,
        'elemental_cast_speed' => 26,
        'elemental_power' => 27,
        'off_affliction_cast_speed' => 28,
        'off_affliction_power' => 29,
        'def_affliction_cast_speed' => 30,
        'def_affliction_power' => 31,
        'heal_cast_speed' => 32,
        'heal_power' => 33,
    );
    // resource grade
    const GRADE_BASIC = 20; // average, plain
    const GRADE_FINE = 35; // prime
    const GRADE_CHOICE = 50; // select
    const GRADE_EXCELLENT = 65; // superb
    const GRADE_SUPREME = 80; // magnificient

    //
    static function uxt_grade($grade)
    {
        switch ($grade) {
            case self::GRADE_BASIC        :
                return 'uiItemRMClass0';
            case self::GRADE_FINE        :
                return 'uiItemRMClass1';
            case self::GRADE_CHOICE        :
                return 'uiItemRMClass2';
            case self::GRADE_EXCELLENT    :
                return 'uiItemRMClass3';
            case self::GRADE_SUPREME    :
                return 'uiItemRMClass4';
        }
        return 'NotExist:grade #' . $grade;
    }
    //
    // item quality - this is actually texture id used.
    const GRADE_BQ = 0;
    const GRADE_MQ = 1; // also tekorn/maga/greslin/armilo
    const GRADE_HQ = 2; // also vedice/cheng/egiros/rubbarn
    //
    // resource ecosystem
    const ECO_COMMON = 0; // basic/fine
    const ECO_DESERT = 1;
    const ECO_FOREST = 2;
    const ECO_LAKE = 3;
    const ECO_JUNGLE = 4;
    //                        = 5;
    const ECO_PR = 6;

    //
    static function uxt_ecosystem($eco)
    {
        switch ($eco) {
            case self::ECO_COMMON    :
                return 'ecosysCommonEcosystem';
            case self::ECO_DESERT    :
                return 'ecosysDesert';
            case self::ECO_FOREST    :
                return 'ecosysForest';
            case self::ECO_LAKE        :
                return 'ecosysLacustre';
            case self::ECO_JUNGLE    :
                return 'ecosysJungle';
            // 5
            case self::ECO_PR        :
                return 'ecosysPrimaryRoot';
        }
        return 'NotExist:eco #' . $eco; // Could use 'ecosysUnknown';
    }
    //
    // item race
    // <code/ryzom/common/game_share/item_origin.h>
    const RACE_COMMON = 0; // outpost
    const RACE_FYROS = 1; // desert
    const RACE_MATIS = 2; // forest
    const RACE_TRYKER = 3; // lake
    const RACE_ZORAI = 4; // jungle
    const RACE_REFUGEE = 5;
    const RACE_NPC = 6; // tribe
    const RACE_KAMI = 7;
    const RACE_KARA = 8;
    //
    // item or resource color
    const COLOR_RED = 0;
    const COLOR_BEIGE = 1;
    const COLOR_GREEN = 2;
    const COLOR_TURQUOISE = 3;
    const COLOR_BLUE = 4;
    const COLOR_PURPLE = 5;
    const COLOR_WHITE = 6;
    const COLOR_BLACK = 7;

    //
    static function uxt_color($color)
    {
        switch ($color) {
            case self::COLOR_RED        :
                return 'mpcolRed';
            case self::COLOR_BEIGE        :
                return 'mpcolBeige';
            case self::COLOR_GREEN        :
                return 'mpcolGreen';
            case self::COLOR_TURQUOISE    :
                return 'mpcolTurquoise';
            case self::COLOR_BLUE        :
                return 'mpcolBlue';
            case self::COLOR_PURPLE        :
                return 'mpcolPurple';
            case self::COLOR_WHITE        :
                return 'mpcolWhite';
            case self::COLOR_BLACK        :
                return 'mpcolBlack';
        }
        return 'NotExist:color #' . $color;
    }

    //
    // creature race from <game_share/people_pd.h>
    const PEOPLE_HUMANOID      = 0;
    //
    const PEOPLE_PLAYABLE      = 0;
    const PEOPLE_FYROS         = 0;
    const PEOPLE_MATIS         = 1;
    const PEOPLE_TRYKER        = 2;
    const PEOPLE_ZORAI         = 3;
    const PEOPLE_ENDPLAYABLE   = 4;
    //
    const PEOPLE_KARAVAN       = 4;
    const PEOPLE_TRIBE         = 5;
    const PEOPLE_COMMON        = 6;
    const PEOPLE_ENDHUMANOID   = 7;
    //
    const PEOPLE_CREATURE      = 7;
    //
    const PEOPLE_FAUNA         = 7;
    const PEOPLE_ARMA          = 7;
    const PEOPLE_BALDUSE       = 8;
    const PEOPLE_BUL           = 9;
    const PEOPLE_CAPRYNI       = 10;
    const PEOPLE_CHONARI       = 11;
    const PEOPLE_CLAPCLAP      = 12;
    const PEOPLE_COCOCLAW      = 13;
    const PEOPLE_CUTE          = 14;
    const PEOPLE_DAG           = 15;
    const PEOPLE_DIRANAK       = 16;
    const PEOPLE_ESTRASSON     = 17;
    const PEOPLE_FILIN         = 18;
    const PEOPLE_FRAHAR        = 19;
    const PEOPLE_GIBBAI        = 20;
    const PEOPLE_HACHTAHA      = 21;
    const PEOPLE_JUNGLER       = 22;
    const PEOPLE_KAKTY         = 23;
    const PEOPLE_KALAB         = 24;
    const PEOPLE_KAMI          = 25;
    const PEOPLE_KAZOAR        = 26;
    const PEOPLE_KITIN         = 27;
    //
    const PEOPLE_KITINS        = 28;
    const PEOPLE_KITIFLY       = 28;
    const PEOPLE_KITIHANK      = 29;
    const PEOPLE_KITIHARAK     = 30;
    const PEOPLE_KITIKIL       = 31;
    const PEOPLE_KITIMANDIB    = 32;
    const PEOPLE_KITINAGAN     = 33;
    const PEOPLE_KITINEGA      = 34;
    const PEOPLE_KITINOKTO     = 35;
    const PEOPLE_ENDKITINS     = 36;
    //
    const PEOPLE_LIGHTBIRD     = 36;
    const PEOPLE_MEKTOUB       = 37;
    const PEOPLE_MEKTOUBPACKER = 38;
    const PEOPLE_MEKTOUBMOUNT  = 39;
    const PEOPLE_PUCETRON      = 40;
    const PEOPLE_REGUS         = 41;
    const PEOPLE_RYZERB        = 42;
    const PEOPLE_RYZOHOLO      = 43;
    const PEOPLE_RYZOHOLOK     = 44;
    const PEOPLE_VAMPIGNON     = 45;
    const PEOPLE_VARINX        = 46;
    const PEOPLE_YBER          = 47;
    const PEOPLE_ZERX          = 48;
    const PEOPLE_RACE_C1       = 49;
    const PEOPLE_RACE_C2       = 50;
    const PEOPLE_RACE_C3       = 51;
    const PEOPLE_RACE_C4       = 52;
    const PEOPLE_RACE_C5       = 53;
    const PEOPLE_RACE_C6       = 54;
    const PEOPLE_RACE_C7       = 55;
    const PEOPLE_RACE_H1       = 56;
    const PEOPLE_RACE_H2       = 57;
    const PEOPLE_RACE_H3       = 58;
    const PEOPLE_RACE_H4       = 59;
    const PEOPLE_RACE_H5       = 60;
    const PEOPLE_RACE_H6       = 61;
    const PEOPLE_RACE_H7       = 62;
    const PEOPLE_RACE_H8       = 63;
    const PEOPLE_RACE_H9       = 64;
    const PEOPLE_RACE_H10      = 65;
    const PEOPLE_RACE_H11      = 66;
    const PEOPLE_RACE_H12      = 67;
    const PEOPLE_ENDFAUNA      = 68;
    //
    const PEOPLE_FLORA         = 68;
    const PEOPLE_CEPHALOPLANT  = 68;
    const PEOPLE_ELECTROALGS   = 69;
    const PEOPLE_PHYTOPSY      = 70;
    const PEOPLE_SAPENSLAVER   = 71;
    const PEOPLE_SPITTINGWEEDS = 72;
    const PEOPLE_SWARMPLANTS   = 73;
    const PEOPLE_ENDFLORA      = 74;
    //
    const PEOPLE_GOO           = 74;
    // goo mobs starting from GooArma=74, ending with GooSwarmplants=140
    //
    // GooKitin is out-of-order, normal list goes: Kazoar, Kitin, Kitifly
    // GooKazoar = 93;
    // GooKitifly = 94;
    // GooKitihank = 95;
    // GooKitiharak = 96;
    // GooKitikil = 97;
    // GooKitimandib = 98;
    // GooKitin = 99;
    // GooKitinagan = 100;
    // GooKitinega = 101;
    // GooKitinokto = 102;
    //
    const PEOPLE_ENDGOO        = 141;
    //
    const PEOPLE_ENDCREATURE   = 141;

    //
    // sbrick 'action_nature'
    const ACTION_FIGHT = 0;
    const ACTION_OFFENSIVE_MAGIC = 1;
    const ACTION_CURATIVE_MAGIC = 2;
    const ACTION_CRAFT = 3;
    const ACTION_HARVEST = 4;
    const ACTION_SEARCH_MP = 5;
    const ACTION_DODGE = 6;
    const ACTION_PARRY = 7;
    const ACTION_SHIELD_USE = 8;
    const ACTION_RECHARGE = 9;
    const ACTION_NEUTRAL = 10;

    // game_share/characteristics.h
    static $characteristic_to_int = array(
        'constitution' => 0,
        'metabolism' => 1,
        'intelligence' => 2,
        'wisdom' => 3,
        'strength' => 4,
        'wellbalanced' => 5,
        'dexterity' => 6,
        'will' => 7,
    );

    /**
     * Return translated consumable effects from $sheet for quality.
     * $sheet can be either string sheet id or item array from ryzom_item_info()
     *
     * @param string|array $sheet
     * @param int $quality
     * @param string $lang
     *
     * @return array
     */
    static function consumable_effects($sheet, $quality, $lang)
    {
        if (!is_array($sheet)) {
            $sheet = ryzom_item_info($sheet);
        }

        if ($sheet === false || empty($sheet['properties'])) {
            return array();
        }

        $effects = array();
        foreach ($sheet['properties'] as $property) {
            $params = explode(':', $property);
            if (empty($params)) {
                continue;
            }

            $name = strtoupper(array_shift($params));
            switch ($name) {
                case 'SP_CHG_CHARAC':
                    if (isset(self::$characteristic_to_int[strtolower($params[0])])) {
                        $ui = ryzom_translate('uiCaracId' . self::$characteristic_to_int[strtolower($params[0])] . '.uxt', $lang);
                        $bonus = (int)($params[1] * $quality + $params[2]);
                        $time = (int)$params[3];
                        if ($bonus > 0) {
                            $tt = ryzom_translate('uiItemConsumableEffectUpCharac.uxt', $lang);
                        } else {
                            $tt = ryzom_translate('uiItemConsumableEffectDownCharac.uxt', $lang);
                        }

                        $effects[] = strtr($tt, array(
                            '%charac' => $ui,
                            '%bonus' => $bonus,
                            '%minutes' => (int)($time / 60),
                            '%secondes' => (int)($time % 60),
                        ));
                    }
                    break;
                case 'SP_LIFE_AURA':
                case 'SP_LIFE_AURA2':
                case 'SP_STAMINA_AURA':
                case 'SP_STAMINA_AURA2':
                case 'SP_SAP_AURA':
                case 'SP_SAP_AURA2':
                    $uiKeys = array(
                        'SP_LIFE_AURA' => 'uiItemConsumableEffectLifeAura.uxt',
                        'SP_LIFE_AURA2' => 'uiItemConsumableEffectLifeAura.uxt',
                        'SP_STAMINA_AURA' => 'uiItemConsumableEffectStaminaAura.uxt',
                        'SP_STAMINA_AURA2' => 'uiItemConsumableEffectStaminaAura.uxt',
                        'SP_SAP_AURA' => 'uiItemConsumableEffectSapAura.uxt',
                        'SP_SAP_AURA2' => 'uiItemConsumableEffectSapAura.uxt',
                    );

                    $tt = ryzom_translate($uiKeys[$name], $lang);

                    if (substr($name, -1) == '2') {
                        $params[0] *= $quality;
                        $params[3] = 0;
                        $params[4] = 0;
                    }
                    $effects[] = strtr($tt, array(
                        '%modifier' => (int)$params[0],
                        '%minutes' => (int)($params[1] / 60),
                        '%secondes' => (int)($params[1] % 60),
                        '%radius' => (int)$params[2],
                        '%targetDisableTime' => (int)$params[3],
                        '%userDisableTime' => (int)$params[4],
                    ));
                    break;
                case 'SP_MOD_DEFENSE':
                case 'SP_MOD_MELEE_SUCCESS':
                case 'SP_MOD_RANGE_SUCCESS':
                case 'SP_MOD_MAGIC_SUCCESS':
                case 'SP_MOD_CRAFT_SUCCESS':
                case 'SP_MOD_FORAGE_SUCCESS':
                    $array = array(
                        'SP_MOD_DEFENSE_SUCCESS' => array(
                            0 => 'uiItemConsumableEffectModDefenseSuccess.uxt',
                            'dodge' => 'uiItemConsumableEffectModDodgeSuccess.uxt',
                            'parry' => 'uiItemConsumableEffectModParrySuccess.uxt',
                        ),
                        'SP_MOD_MELEE_SUCCESS' => 'uiItemConsumableEffectModMeleeSuccess.uxt',
                        'SP_MOD_RANGE_SUCCESS' => 'uiItemConsumableEffectModRangeSuccess.uxt',
                        'SP_MOD_MAGIC_SUCCESS' => 'uiItemConsumableEffectModMagicSuccess.uxt',
                        'SP_MOD_CRAFT_SUCCESS' => 'uiItemConsumableEffectModCraftSuccess.uxt',
                        'SP_MOD_FORAGE_SUCCESS' => array(
                            'commonecosystem' => 'uiItemConsumableEffectModForageSuccess.uxt',
                            'desert' => 'uiItemConsumableEffectModDesertForageSuccess.uxt',
                            'forest' => 'uiItemConsumableEffectModForestForageSuccess.uxt',
                            'lacustre' => 'uiItemConsumableEffectModLacustreForageSuccess.uxt',
                            'jungle' => 'uiItemConsumableEffectModJungleForageSuccess.uxt',
                            'primaryroot' => 'uiItemConsumableEffectModPrimaryRootForageSuccess.uxt',
                        ),
                    );
                    if (isset($array[$name])) {
                        $tt = $array[$name];
                        $index = 0;
                        if (is_array($tt)) {
                            if (isset($tt[strtolower($params[0])])) {
                                $tt = $tt[strtolower($params[0])];
                            } else if (isset($tt[0])) {
                                $tt = $tt[0];
                            } else {
                                $tt = false;
                            }
                            $index = 1;
                        }
                        if ($tt !== false) {
                            $tt = ryzom_translate($tt, $lang);
                            $effects[] = strtr($tt, array(
                                '%modifier' => ($params[$index] * $quality + $params[$index + 1]),
                                '%minutes' => (int)($params[$index + 2] / 60),
                                '%secondes' => (int)($params[$index + 2] % 60),
                            ));
                        }
                    }
                    break;
                default:
                    //
                    break;
            }
        }

        return $effects;
    }

    /**
     * Return translated special effects from $sheet.
     * $sheet can be either string sheet id or item array from ryzom_item_info()
     *
     * @param string|array $sheet
     * @param string $lang
     *
     * @return array
     */
    static function special_effects($sheet, $lang)
    {
        if (!is_array($sheet)) {
            $sheet = ryzom_item_info($sheet);
        }

        if ($sheet === false || empty($sheet['effects'])) {
            return array();
        }

        $effects = array();
        foreach ($sheet['effects'] as $effect) {
            $params = explode(":", $effect);
            if (empty($params)) {
                continue;
            }

            $name = strtoupper(array_shift($params));

            $tt = ryzom_translate('uiItemFX_' . $name . '.uxt', $lang);
            $chunks = preg_split('/(%[pnrs])\d?/', $tt, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $i = 0;
            $ret = '';
            foreach ($chunks as $c) {
                switch ($c) {
                    case '%p':
                        $ret .= sprintf('%.1f', $params[$i] * 100);
                        $i++;
                        break;
                    case '%n':
                        $ret .= (int)$params[$i];
                        $i++;
                        break;
                    case '%r':
                        $ret .= sprintf('%.1f', $params[$i]);
                        $i++;
                        break;
                    case '%s':
                        $ret .= $params[$i];
                        $i++;
                        break;
                    default:
                        $ret .= $c;
                        break;
                }
            }

            $effects[] = $ret;
        }

        return $effects;
    }

    /**
     * Return dataset file where sheet info is located
     *
     * @param string $sheet creature, title, item, etc
     * @param string|int $extra optional full sheet name or numerid id
     *
     * @return string
     */
    static public function get_dataset_name($sheet, $extra = false) {
        switch($sheet) {
        case 'creature':
            $keys = array_flip(str_split('abcdefghijklmnopqrstuvwxyz', 1));
            $k = substr($extra, 0, 1);
            if (!isset($keys[$k])) {
                $k = '_';
            }
            $ret = 'creature-'.$k;
            break;
        default:
            $ret = $sheet;
        }
        return $ret;
    }
}

/**
 * Get $sheetid translation from .suffix language file.
 * Include language file on first run and cache it.
 *
 * NOTE: sheetid is converted to lowercase.
 *       line breaks must be handled separately. they marked as "\n"
 *
 * @param string sheetid
 * @param string lang
 * @param string|int $index return 'name' column by default if it exists.
 *                     for 'title', 0 == male, 1 == female
 *                     for 'title', if 'women_name' it empty, then 'name' is returned.
 *
 * @return string translated text, error message if language file or sheet id is not found
 */
function ryzom_translate($sheetid, $lang, $index = 0)
{
    // memory usage for 1 language is around:
    // 4.7MiB creature, 70KiB faction, 8.5MiB item, 800KiB outpost, 600KiB place, 6MiB sbrick, 1MiB skill, 5MiB sphrase, 2MiB title, 4MiB uxt
    static $cache = array();

    // break up sheetid
    $_id = strtolower($sheetid);
    $_ext = strtolower(substr(strrchr($_id, '.'), 1));
    if ($_ext === false || $_ext == '') {
        $_ext = 'title'; // 'title' should be only one without 'dot' in sheetid
    } else {
        $_id = substr($_id, 0, strlen($_id) - strlen($_ext) - 1);
    }

    // remap
    if ($_ext == 'sitem') {
        $_ext = 'item';
    }

    // 'Neutral' is not included in faction translation, so do it here
    if ($_ext == 'faction' && $_id == 'neutral') {
        if ($lang == 'fr') {
            return 'Neutre';
        } else {
            return 'Neutral';
        }
    }

    // include translation file if needed
    if (!isset($cache[$_ext][$lang])) {
        // use serialize/unserialize saves lot of memory
        $file = sprintf('%s/words_%s_%s.serial', RYZOM_EXTRA_SHEETS_CACHE, $lang, $_ext);
        $cache[$_ext][$lang] = ryzom_extra_load_dataset($file);
    }

    // remap id if full sheetid user requested is found
    if (isset($cache[$_ext][$lang][$sheetid])) {
        $_id = $sheetid;
    }

    // check if translation is there
    if (!isset($cache[$_ext][$lang][$_id])) {
        return 'NotFound:(' . $_ext . ')' . $lang . '.' . $sheetid;
    }

    // return translation - each may have different array 'key' for translation
    $word = $cache[$_ext][$lang][$_id];

    if ($index === 0) {
        $index = 'name';
    } else if ($_ext === 'title' && $index === 1) {
        $index = empty($word['women_name']) ? 'name' : 'women_name';
    }

    if (isset($word[$index])) {
        return $word[$index];
    }

    // unknown column index
    return 'Unknown:' . $_ext . '.' . $_id;
}

/**
 * Converts binary sheet_id to string format
 *
 * @param  int   numeric sheet_id
 *
 * @return mixed sheetid in string format or boolean FALSE if lookup failed
 */
function ryzom_sheetid_bin($sid_bin)
{
    // full list is around 120MiB
    static $cache = array();

    $idx = floor(intval($sid_bin) / 1000000);
    if (!isset($cache[$idx])) {
        $cache[$idx] = ryzom_extra_load_dataset(sprintf('%s/sheets-%02x.serial', RYZOM_EXTRA_SHEETS_CACHE, $idx));
    }
    if (isset($cache[$idx][$sid_bin])) {
        return $cache[$idx][$sid_bin];
    }
    return false;
}

/**
 * Return building info based building id from API XML file
 * If building_id is unknown, then return empty array
 *
 * @param int $building_id
 *
 * @return array
 */
function ryzom_building_info($building_id)
{
    static $cache = array();
    if (empty($cache)) {
        $file = sprintf('%s/buildings.inc.php', RYZOM_EXTRA_PATH);
        if (!file_exists($file)) {
            throw new Exception('Date file [' . $file . '] not found');
        }
        $cache = include($file);
    }
    if (!isset($cache[$building_id])) {
        $result = array();
    } else {
        $result = $cache[$building_id];
    }
    return $result;
}

/**
 * Returns sheetid details
 *
 * @param $sheetid - with or without '.sitem'
 * @param $extra - for items, also include craft plan to '_craftplan' index
 *                   for resources, include stats to '_stats' index
 *
 * @return array
 */
function ryzom_item_info($sheetid, $extra = false)
{
    static $cache = array(); // ~ 20MiB, items

    // include data file if needed
    if (empty($cache)) {
        // use serialize/unserialize saves lot of memory
        $file = sprintf('%s/items.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }

    $_id = strtolower($sheetid);
    if (preg_match('/^(.*)\.sitem$/', $_id, $m)) {
        $_id = $m[1];
    }

    if (!isset($cache[$_id])) {
        $result = false;
        return $result;
    }
    $result = $cache[$_id];

    // fix some id's
    if (isset($result['craftplan'])) {
        $result['craftplan'] .= '.sbrick';
    }
    if (isset($result['skill'])) {
        $result['skill'] .= '.skill';
    }
    $result['sheetid'] .= '.sitem';

    // if item type is Resource, then also include stats
    if ($extra == true) {
        if ($result['type'] == RyzomExtra::TYPE_RESOURCE) {
            $result['_stats'] = ryzom_resource_stats($_id);
        } else if (isset($result['craftplan'])) {
            $result['_craftplan'] = ryzom_craftplan($result['craftplan']);
        }
    }

    return $result;
}

/**
 * Return resource craft stats like durability/lightness, etc
 *
 * @param $sheetid - with or without '.sitem'
 *
 * @return mixed - FALSE if $sheetid not found
 */
function ryzom_resource_stats($sheetid)
{
    static $cache; // ~20MiB, resource stats cache

    if (empty($cache)) {
        $file = sprintf('%s/resource_stats.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }

    $_id = strtolower($sheetid);
    if (preg_match('/^(.*)\.sitem$/', $_id, $m)) {
        $_id = $m[1];
    }

    if (isset($cache[$_id])) {
        $result = $cache[$_id]['stats'];
    } else {
        $result = false;
    }
    return $result;
}

/**
 * Return sbrick details
 *
 * @param $sheetid with or without '.sbrick'
 *
 * @return mixed FALSE if $sheetid not found
 */
function ryzom_sbrick_info($sheetid)
{
    static $cache;

    if (empty($cache)) {
        $file = sprintf('%s/sbrick.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }
    $_id = strtolower($sheetid);
    if (preg_match('/^(.*)\.sbrick$/', $_id, $m)) {
        $_id = $m[1];
    }
    if (!isset($cache[$_id])) {
        $result = false;
        return $result;
    }
    return $cache[$_id];
}

/**
 * Return craft plan
 *
 * @param $sheetid - with or without '.sbrick'
 *
 * @return unknown_type
 */
function ryzom_craftplan($sheetid)
{
    static $cache = array();
    if (empty($cache)) {
        $file = sprintf('%s/craftplan.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }

    $_id = strtolower($sheetid);
    if (preg_match('/^(.*)\.sbrick$/', $_id, $m)) {
        $_id = $m[1];
    }

    if (isset($cache[$_id])) {
        $result = $cache[$_id];
    } else {
        $result = false;
    }
    return $result;
}

/**
 * Return unformatted skilltree list
 *
 * @return unknown_type
 */
function ryzom_skilltree()
{
    static $cache = array();
    if (empty($cache)) {
        $file = sprintf('%s/skilltree.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }

    return $cache;
}

/**
 * Return creature info
 *
 * @param strring $sheetid
 *
 * @return array
 */
function ryzom_creature_info($sheetid){
    static $cache = array(); // ~40MiB (php7), creature

    // include data file if needed
    $fname = RyzomExtra::get_dataset_name('creature', $sheetid);
    if(empty($cache[$fname])){
        $file = RYZOM_EXTRA_SHEETS_CACHE.'/'.$fname.'.serial';
        $cache[$fname] = ryzom_extra_load_dataset($file);
    }

    $_id = strtolower($sheetid);
    if(preg_match('/^(.*)\.creature$/', $_id, $m)){
        $_id=$m[1];
	} else {
		$_id = $sheetid;
	}

    if(!isset($cache[$fname][$_id])){
        $result = false;
        return $result;
    }
    return $cache[$fname][$_id];
}

/**
 * Visual slot index to sheet translation
 *
 * $slot is const from RyzomSheets EVisualSlot class
 *
 * @param int $slot
 * @param int $index
 *
 * @return string|bool
 */
function ryzom_vs_sheet($slot, $index)
{
    $cache = ryzom_extra_load_vs();

    if (isset($cache[$slot][$index])) {
        return $cache[$slot][$index];
    }

    return false;
}

/**
 * Find visual slot index for requested sheet name
 *
 * @param int $slot
 * @param string $sheet
 *
 * @return bool|mixed
 */
function ryzom_vs_index($slot, $sheet)
{
    $cache = ryzom_extra_load_vs();

    if (!isset($cache[$slot])) {
        return false;
    }
    return array_search($sheet, $cache[$slot], true);
}

/**
 * Load visual_slot.serial file
 *
 * @return array
 */
function ryzom_extra_load_vs()
{
    static $cache = array();
    if (empty($cache)) {
        $file = sprintf('%s/visual_slot.serial', RYZOM_EXTRA_SHEETS_CACHE);
        $cache = ryzom_extra_load_dataset($file);
    }
    return $cache;
}

/**
 * Loads dataset and returns result.
 * Does not unmask unserialize/file_get_content warning/notice's
 *
 * throw Exception if file not found
 *
 * @param $file file name with full path
 *
 * @return mixed
 */
function ryzom_extra_load_dataset($file)
{
    if (file_exists($file)) {
        $result = unserialize(file_get_contents($file));
    } else {
        throw new Exception('Data file [' . $file . '] not found');
    }
    return $result;
}
