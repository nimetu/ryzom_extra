<?php

/**
 * Test exported files
 */
class RyzomExtraDataTest extends \PHPUnit\Framework\TestCase
{
	public function testVisualTab() {
		// test not empty
		$vs = ryzom_extra_load_vs();
		if (empty($vs)) {
			$this->fail("Empty visual_slot.tab file.");
		}

		// test structure
		$slot = 1;
		$index = 1;
		$value = 'icfahv.sitem';

		$this->assertEquals($value, $vs[$slot][$index]);
		$this->assertEquals($value, ryzom_vs_sheet($slot, $index));
	}

	/**
	 * Trying to load invalid dataset file.
	 */
	public function testInvalidDataset() {
		$this->expectException('\Exception', 'Invalid data file');
		$ret = ryzom_translate('invalid.!sheet!', 'en');
	}

	/**
	 * Translation with default 'name' column
	 */
	public function testDefaultTranslation() {
		$this->assertEquals('Common', ryzom_translate('common.race', 'en'));
	}

	/**
	 * Test title translation based gender column
	 */
	public function testGenderTitleTranslation() {
		$this->assertEquals('Pikeman', ryzom_translate('pikeman.title', 'en'));
		$this->assertEquals('Pikewoman', ryzom_translate('pikeman.title', 'en', 1));
		$this->assertEquals('Pikewoman', ryzom_translate('pikeman.title', 'en', 'women_name'));
	}

	/**
	 * Error message on invalid sheet id
	 */
	public function testUnknownSheetId() {
		$this->assertEquals('NotFound:(title)en.!invalid!.title', ryzom_translate('!invalid!.title', 'en'));
	}

	/**
	 * Error message on invalid column name
	 */
	public function testUnknownColumn() {
		$this->assertEquals("Unknown:title.homin", ryzom_translate('homin.title', 'en', 'unknown-column'));
	}

	/**
	 * Translation with custom columns
	 *
	 * @dataProvider translationDataProvider
	 */
	function testTranslationWithColumn($sheet, $column, $data) {
		foreach($data as $lang => $expected) {
			$this->assertEquals($expected, ryzom_translate($sheet, $lang, $column));
		}
	}

	/**
	 * Translation with replaced placeholder text
	 *
	 * @dataProvider replacedPlaceholderTextProvider
	 */
	function testReplacedPlaceholderText($sheet, $column, $data) {
		foreach($data as $lang => $expected) {
			$this->assertEquals($expected, ryzom_translate($sheet, $lang, $column));
		}
	}

	/**
	 * Translation data
	 */
	public function translationDataProvider() {
		return [
			['languagename.uxt', 'name', [
				'en' => 'English',
				'fr' => 'Français',
				'de' => 'Deutsch',
				'es' => 'Español',
				'ru' => 'Русский',
			]],
			['gn_apprentice_tracer.title', 'name', [
				'en' => 'Apprentice Tracer',
				'fr' => 'Apprenti Traceur',
				'de' => 'Apprentice Tracer',
				'es' => 'Aprendiz de trazador',
				'ru' => 'Apprentice Tracer',
			]],
			['abf01.sphrase', 'name', [
				'en' => 'Default Attack',
				'fr' => 'Attaque par Défaut',
				'de' => 'Standardangriff',
				'es' => 'Ataque por Defecto',
				'ru' => 'Базовая атака',
			]],
			['acuratebleedingshot.skill', 'name', [
				'en' => 'Accurate Bleeding Shot',
				'fr' => 'Tir précis sanglant',
				'de' => 'Akkurater blutiger Schuss',
				'es' => 'Disparo Sangrante Certero',
				'ru' => 'Точный выстрел с кровопусканием',
			]],
			['hitpoints.score', 'name', [
				'en' => 'HP',
				'fr' => 'Vie',
				'de' => 'Lebenskraft',
				'es' => 'Salud',
				'ru' => 'здоровье',
			]],
			['bfpa01.sbrick', 'name', [
				'en' => 'Default attack',
				'fr' => 'Attaque par Défaut',
				'de' => 'Standardangriff',
				'es' => 'Ataque por Falta o Defecto',
				'ru' => 'Базовая атака',
			]],
			['fyros.race', 'name', [
				'en' => 'Fyros',
				'fr' => 'Fyros',
				'de' => 'Fyros',
				'es' => 'Fyros',
				'ru' => 'файрос',
			]],
			['region_majesticgarden.place', 'name', [
				'en' => 'Majestic Garden',
				'fr' => 'Jardin Majestueux',
				'de' => 'Majestätischer Garten',
				'es' => 'Jardín Majestuoso',
				'ru' => 'Сады Величия',
			]],
			['marauder_light_melee_fighter_b.outpost_squad', 'name', [
				'en' => 'Marauder Warriors Squad',
				'fr' => 'Escouade de guerriers maraudeurs',
				'de' => 'Marodeur-Kriegertruppe',
				'es' => 'Escuadrón de Guerreros Marauder',
				'ru' => 'Отряд мародеров-воинов',
			]],
			['driller_bountybeaches_kami_u1_100a.outpost_building', 'name', [
				'en' => 'Offering for Tree-Bore',
				'fr' => 'Offrande pour Arbre-Vrille',
				'de' => 'Angebot für Baumbohrer',
				'es' => 'Ofrenda para el árbol de cañón',
				'ru' => 'Подношение Древо-буру',
			]],
			['fyros_outpost_04.outpost', 'name', [
				'en' => 'Malmont Farm',
				'fr' => 'Ferme de Malmontagne',
				'de' => 'Malmont-Hof',
				'es' => 'Granja Malmont',
				'ru' => 'Ферма Мальмонт',
			]],
			['barman_bottle.item', 'name', [
				'en' => 'Barman\'s Bottle',
				'fr' => 'Bouteille de barman',
				'de' => 'Barmannsflasche',
				'es' => 'Botella del Tabernero',
				'ru' => 'Бутылка бармена',
			]],
			['fyros.faction', 'name', [
				'en' => 'Fyros',
				'fr' => 'Fyros',
				'de' => 'Fyros',
				'es' => 'Fyros',
				'ru' => 'файросы',
			]],
			['desert.ecosystem', 'name', [
				'en' => 'Desert',
				'fr' => 'Désert',
				'de' => 'Wüste',
				'es' => 'Desierto',
				'ru' => 'Пустыня',
			]],
			['slashing.damagetype', 'name', [
				'en' => 'Slashing',
				'fr' => 'Tranchant',
				'de' => 'Schneide',
				'es' => 'Cortante',
				'ru' => 'Рубящий',
			]],
			['caaga1.creature', 'name', [
				'en' => 'Impure Baldusa',
				'fr' => 'Baldusa impur',
				'de' => 'Erkrankter Baldusa',
				'es' => 'Baldusa Impura',
				'ru' => 'Мерзкая бальдуза',
			]],
			['constitution.characteristic', 'name', [
				'en' => 'constitution',
				'fr' => 'Constitution',
				'de' => 'Konstitution',
				'es' => 'Constitución',
				'ru' => 'телосложение',
			]],
		];
	}

	/**
	 * Translation data with replaced placeholder text
	 */
	public function replacedPlaceholderTextProvider() {
		return [
			// 'Increase Damage 1 - Max Factor: $1INC_DMG'
			['bfma01.sbrick', 'description2', [
				'en' => 'Increase Damage 1 - Max Factor: 2.0',
				'fr' => 'Coup Puissant  1 - Facteur Max. : 2.0',
				'de' => 'Erhöhter Schaden 1, max. Faktor : 2.0',
				'es' => 'Aumenta el Daño 1 - Factor Máximo: 2.0',
				'ru' => 'Повышенный урон 1 - Максимальный фактор 2.0',
			]],
		];
	}
}

