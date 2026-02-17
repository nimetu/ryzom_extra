<?php

class RyzomExtraTest extends \PHPUnit\Framework\TestCase
{
	public function test_uxt_mpft() {
		$this->assertEquals('mpftMpED', RyzomExtra::uxt_mpft(RyzomExtra::MPFT_JEWEL));
		$this->assertEquals('NotExist:bit #999', RyzomExtra::uxt_mpft(999));
	}

	public function test_uxt_grade() {
		$this->assertEquals('uiItemRMClass0', RyzomExtra::uxt_grade(RyzomExtra::GRADE_BASIC));
		$this->assertEquals('uiItemRMClass1', RyzomExtra::uxt_grade(RyzomExtra::GRADE_FINE));
		$this->assertEquals('uiItemRMClass2', RyzomExtra::uxt_grade(RyzomExtra::GRADE_CHOICE));
		$this->assertEquals('uiItemRMClass3', RyzomExtra::uxt_grade(RyzomExtra::GRADE_EXCELLENT));
		$this->assertEquals('uiItemRMClass4', RyzomExtra::uxt_grade(RyzomExtra::GRADE_SUPREME));
		$this->assertEquals('NotExist:grade #999', RyzomExtra::uxt_grade(999));
	}

	public function test_uxt_ecosystem() {
		$this->assertEquals('ecosysCommonEcosystem', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_COMMON));
		$this->assertEquals('ecosysDesert', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_DESERT));
		$this->assertEquals('ecosysForest', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_FOREST));
		$this->assertEquals('ecosysLacustre', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_LAKE));
		$this->assertEquals('ecosysJungle', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_JUNGLE));
		$this->assertEquals('ecosysPrimaryRoot', RyzomExtra::uxt_ecosystem(RyzomExtra::ECO_PR));
		$this->assertEquals('NotExist:eco #7',  RyzomExtra::uxt_ecosystem(7));
	}

	public function test_uxt_color() {
		$this->assertEquals('mpcolRed', RyzomExtra::uxt_color(RyzomExtra::COLOR_RED));
		$this->assertEquals('mpcolBeige', RyzomExtra::uxt_color(RyzomExtra::COLOR_BEIGE));
		$this->assertEquals('mpcolGreen', RyzomExtra::uxt_color(RyzomExtra::COLOR_GREEN));
		$this->assertEquals('mpcolTurquoise', RyzomExtra::uxt_color(RyzomExtra::COLOR_TURQUOISE));
		$this->assertEquals('mpcolBlue', RyzomExtra::uxt_color(RyzomExtra::COLOR_BLUE));
		$this->assertEquals('mpcolPurple', RyzomExtra::uxt_color(RyzomExtra::COLOR_PURPLE));
		$this->assertEquals('mpcolWhite', RyzomExtra::uxt_color(RyzomExtra::COLOR_WHITE));
		$this->assertEquals('mpcolBlack', RyzomExtra::uxt_color(RyzomExtra::COLOR_BLACK));
		$this->assertEquals('NotExist:color #8', RyzomExtra::uxt_color(8));
	}

	public function test_consumable_effect() {
		$this->assertEquals(
			[
				"Gives a bonus of @{2F2F}21@{FFFF} points(s) to your @{2F2F}Metabolism@{FFFF}\nfor 60 min and 0 sec.",
				"Gives a bonus of @{2F2F}21@{FFFF} points(s) to your @{2F2F}Constitution@{FFFF}\nfor 60 min and 0 sec.",
			],
			RyzomExtra::consumable_effects('ipoc_hp.sitem', 213, 'en'),
		);
		$this->assertEquals(
			["Modifies your Defense by 213 point(s) for 10 min and 0 sec."],
			RyzomExtra::consumable_effects('test_conso_defense.sitem', 213, 'en'),
		);
		$this->assertEquals(
			["Modifies your Melee Fight skills by 213 point(s) for 10 min and 0 sec."],
			RyzomExtra::consumable_effects('test_conso_melee_success.sitem', 213, 'en'),
		);
		$this->assertEquals(
			["Modifies your Desert Forage skills by 213 point(s) for 10 min and 0 sec."],
			RyzomExtra::consumable_effects('test_conso_desert_forage_success.sitem', 213, 'en'),
		);
		$this->assertEquals(
			[
				"An @{2F2F}Aura of Sap@{FFFF} with a bonus of @{2F2F}400@{FFFF} for 0 min and 10 sec with radius of 10 m.\nDisabled for target: 70 sec, user: 70 sec.",
				"An @{2F2F}Aura of Life@{FFFF} with a bonus of @{2F2F}200@{FFFF} for 0 min and 10 sec with radius of 10 m.\nDisabled for target: 70 sec, user: 70 sec.",
			],
			RyzomExtra::consumable_effects('rpjobitem_202_c1.sitem', 213, 'en'),
		);
		$this->assertEquals(
			[
				"An @{2F2F}Aura of Sap@{FFFF} with a bonus of @{2F2F}401@{FFFF} for 5 min and 1 sec with radius of 11 m.\nDisabled for target: 70 sec, user: 71 sec.",
			],
			RyzomExtra::consumable_effects(['properties' => ['SP_SAP_AURA:401:301:11:70:71']], 200, 'en')
		);
		$this->assertEquals([], RyzomExtra::consumable_effects('fy_helmet_01.sitem', 213, 'en'));
	}

	public function test_special_effects() {
		$this->assertEquals(
			["@{2F2F}5.0%@{FFFF} chance of increasing the quantity of foraged materials per action by @{2F2F}100.0%@{FFFF}."],
			RyzomExtra::special_effects(['effects' => ['ise_forage_add_rm:0.05:1']], 'en'),
		);
		$this->assertEquals(
			["@{2F2F}+10.0%@{FFFF} chance of performing a critical hit."],
			RyzomExtra::special_effects('icokamm1bm_1.sitem', 'en'),
		);
		// TODO: no uiItemFX_*.uxt translations that use these
		// %n
		// %r
		// %s
	}

	public function test_get_dataset_name() {
		$this->assertEquals('creature-m', RyzomExtra::get_dataset_name('creature', 'mfa1'));
		$this->assertEquals('creature-z', RyzomExtra::get_dataset_name('creature', 'zofa1'));
		$this->assertEquals('creature-_', RyzomExtra::get_dataset_name('creature', '123'));
		$this->assertEquals('test', RyzomExtra::get_dataset_name('test', ''));
	}
}
