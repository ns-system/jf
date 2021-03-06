<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnequalLoanLedgers extends Migration
{

    /**
     * マイグレーション実行
     *
     * @return void
     */
    public $tableName = 'unequal_loan_ledgers';
    public $connect   = 'mysql_zenon';

    public function up() {
        Schema::connection($this->connect)->create($this->tableName, function(Blueprint $table) {
            $table->increments("id");
            $table->integer("record_state")->index();
            $table->integer("prefecture_code")->index();
            $table->integer("organization_code")->index();
            $table->integer("store_number")->index();
            $table->double("loan_account_number")->index();
            $table->integer("record_number")->index();
            $table->integer("unequal_state_01");
            $table->integer("unequal_state_02");
            $table->integer("unequal_state_03");
            $table->integer("unequal_state_04");
            $table->integer("unequal_state_05");
            $table->integer("unequal_state_06");
            $table->integer("unequal_state_07");
            $table->integer("unequal_state_08");
            $table->integer("unequal_individual_state_001");
            $table->integer("unequal_individual_state_002");
            $table->integer("unequal_individual_state_003");
            $table->integer("unequal_individual_state_004");
            $table->integer("unequal_individual_state_005");
            $table->integer("unequal_individual_state_006");
            $table->integer("unequal_individual_state_007");
            $table->integer("unequal_individual_state_008");
            $table->date("contract_term_1_on")->nullable();
            $table->double("repayment_principal_1");
            $table->integer("unequal_individual_state_011");
            $table->integer("unequal_individual_state_012");
            $table->integer("unequal_individual_state_013");
            $table->integer("unequal_individual_state_014");
            $table->integer("unequal_individual_state_015");
            $table->integer("unequal_individual_state_016");
            $table->integer("unequal_individual_state_017");
            $table->integer("unequal_individual_state_018");
            $table->date("contract_term_2_on")->nullable();
            $table->double("repayment_principal_2");
            $table->integer("unequal_individual_state_021");
            $table->integer("unequal_individual_state_022");
            $table->integer("unequal_individual_state_023");
            $table->integer("unequal_individual_state_024");
            $table->integer("unequal_individual_state_025");
            $table->integer("unequal_individual_state_026");
            $table->integer("unequal_individual_state_027");
            $table->integer("unequal_individual_state_028");
            $table->date("contract_term_3_on")->nullable();
            $table->double("repayment_principal_3");
            $table->integer("unequal_individual_state_031");
            $table->integer("unequal_individual_state_032");
            $table->integer("unequal_individual_state_033");
            $table->integer("unequal_individual_state_034");
            $table->integer("unequal_individual_state_035");
            $table->integer("unequal_individual_state_036");
            $table->integer("unequal_individual_state_037");
            $table->integer("unequal_individual_state_038");
            $table->date("contract_term_4_on")->nullable();
            $table->double("repayment_principal_4");
            $table->integer("unequal_individual_state_041");
            $table->integer("unequal_individual_state_042");
            $table->integer("unequal_individual_state_043");
            $table->integer("unequal_individual_state_044");
            $table->integer("unequal_individual_state_045");
            $table->integer("unequal_individual_state_046");
            $table->integer("unequal_individual_state_047");
            $table->integer("unequal_individual_state_048");
            $table->date("contract_term_5_on")->nullable();
            $table->double("repayment_principal_5");
            $table->integer("unequal_individual_state_051");
            $table->integer("unequal_individual_state_052");
            $table->integer("unequal_individual_state_053");
            $table->integer("unequal_individual_state_054");
            $table->integer("unequal_individual_state_055");
            $table->integer("unequal_individual_state_056");
            $table->integer("unequal_individual_state_057");
            $table->integer("unequal_individual_state_058");
            $table->date("contract_term_6_on")->nullable();
            $table->double("repayment_principal_6");
            $table->integer("unequal_individual_state_061");
            $table->integer("unequal_individual_state_062");
            $table->integer("unequal_individual_state_063");
            $table->integer("unequal_individual_state_064");
            $table->integer("unequal_individual_state_065");
            $table->integer("unequal_individual_state_066");
            $table->integer("unequal_individual_state_067");
            $table->integer("unequal_individual_state_068");
            $table->date("contract_term_7_on")->nullable();
            $table->double("repayment_principal_7");
            $table->integer("unequal_individual_state_071");
            $table->integer("unequal_individual_state_072");
            $table->integer("unequal_individual_state_073");
            $table->integer("unequal_individual_state_074");
            $table->integer("unequal_individual_state_075");
            $table->integer("unequal_individual_state_076");
            $table->integer("unequal_individual_state_077");
            $table->integer("unequal_individual_state_078");
            $table->date("contract_term_8_on")->nullable();
            $table->double("repayment_principal_8");
            $table->integer("unequal_individual_state_081");
            $table->integer("unequal_individual_state_082");
            $table->integer("unequal_individual_state_083");
            $table->integer("unequal_individual_state_084");
            $table->integer("unequal_individual_state_085");
            $table->integer("unequal_individual_state_086");
            $table->integer("unequal_individual_state_087");
            $table->integer("unequal_individual_state_088");
            $table->date("contract_term_9_on")->nullable();
            $table->double("repayment_principal_9");
            $table->integer("unequal_individual_state_091");
            $table->integer("unequal_individual_state_092");
            $table->integer("unequal_individual_state_093");
            $table->integer("unequal_individual_state_094");
            $table->integer("unequal_individual_state_095");
            $table->integer("unequal_individual_state_096");
            $table->integer("unequal_individual_state_097");
            $table->integer("unequal_individual_state_098");
            $table->date("contract_term_10_on")->nullable();
            $table->double("repayment_principal_10");
            $table->integer("unequal_individual_state_101");
            $table->integer("unequal_individual_state_102");
            $table->integer("unequal_individual_state_103");
            $table->integer("unequal_individual_state_104");
            $table->integer("unequal_individual_state_105");
            $table->integer("unequal_individual_state_106");
            $table->integer("unequal_individual_state_107");
            $table->integer("unequal_individual_state_108");
            $table->date("contract_term_11_on")->nullable();
            $table->double("repayment_principal_11");
            $table->integer("unequal_individual_state_111");
            $table->integer("unequal_individual_state_112");
            $table->integer("unequal_individual_state_113");
            $table->integer("unequal_individual_state_114");
            $table->integer("unequal_individual_state_115");
            $table->integer("unequal_individual_state_116");
            $table->integer("unequal_individual_state_117");
            $table->integer("unequal_individual_state_118");
            $table->date("contract_term_12_on")->nullable();
            $table->double("repayment_principal_12");
            $table->integer("unequal_individual_state_121");
            $table->integer("unequal_individual_state_122");
            $table->integer("unequal_individual_state_123");
            $table->integer("unequal_individual_state_124");
            $table->integer("unequal_individual_state_125");
            $table->integer("unequal_individual_state_126");
            $table->integer("unequal_individual_state_127");
            $table->integer("unequal_individual_state_128");
            $table->date("contract_term_13_on")->nullable();
            $table->double("repayment_principal_13");
            $table->integer("unequal_individual_state_131");
            $table->integer("unequal_individual_state_132");
            $table->integer("unequal_individual_state_133");
            $table->integer("unequal_individual_state_134");
            $table->integer("unequal_individual_state_135");
            $table->integer("unequal_individual_state_136");
            $table->integer("unequal_individual_state_137");
            $table->integer("unequal_individual_state_138");
            $table->date("contract_term_14_on")->nullable();
            $table->double("repayment_principal_14");
            $table->integer("unequal_individual_state_141");
            $table->integer("unequal_individual_state_142");
            $table->integer("unequal_individual_state_143");
            $table->integer("unequal_individual_state_144");
            $table->integer("unequal_individual_state_145");
            $table->integer("unequal_individual_state_146");
            $table->integer("unequal_individual_state_147");
            $table->integer("unequal_individual_state_148");
            $table->date("contract_term_15_on")->nullable();
            $table->double("repayment_principal_15");
            $table->integer("unequal_individual_state_151");
            $table->integer("unequal_individual_state_152");
            $table->integer("unequal_individual_state_153");
            $table->integer("unequal_individual_state_154");
            $table->integer("unequal_individual_state_155");
            $table->integer("unequal_individual_state_156");
            $table->integer("unequal_individual_state_157");
            $table->integer("unequal_individual_state_158");
            $table->date("contract_term_16_on")->nullable();
            $table->double("repayment_principal_16");
            $table->integer("unequal_individual_state_161");
            $table->integer("unequal_individual_state_162");
            $table->integer("unequal_individual_state_163");
            $table->integer("unequal_individual_state_164");
            $table->integer("unequal_individual_state_165");
            $table->integer("unequal_individual_state_166");
            $table->integer("unequal_individual_state_167");
            $table->integer("unequal_individual_state_168");
            $table->date("contract_term_17_on")->nullable();
            $table->double("repayment_principal_17");
            $table->integer("unequal_individual_state_171");
            $table->integer("unequal_individual_state_172");
            $table->integer("unequal_individual_state_173");
            $table->integer("unequal_individual_state_174");
            $table->integer("unequal_individual_state_175");
            $table->integer("unequal_individual_state_176");
            $table->integer("unequal_individual_state_177");
            $table->integer("unequal_individual_state_178");
            $table->date("contract_term_18_on")->nullable();
            $table->double("repayment_principal_18");
            $table->integer("unequal_individual_state_181");
            $table->integer("unequal_individual_state_182");
            $table->integer("unequal_individual_state_183");
            $table->integer("unequal_individual_state_184");
            $table->integer("unequal_individual_state_185");
            $table->integer("unequal_individual_state_186");
            $table->integer("unequal_individual_state_187");
            $table->integer("unequal_individual_state_188");
            $table->date("contract_term_19_on")->nullable();
            $table->double("repayment_principal_19");
            $table->integer("unequal_individual_state_191");
            $table->integer("unequal_individual_state_192");
            $table->integer("unequal_individual_state_193");
            $table->integer("unequal_individual_state_194");
            $table->integer("unequal_individual_state_195");
            $table->integer("unequal_individual_state_196");
            $table->integer("unequal_individual_state_197");
            $table->integer("unequal_individual_state_198");
            $table->date("contract_term_20_on")->nullable();
            $table->double("repayment_principal_20");
            $table->integer("unequal_individual_state_201");
            $table->integer("unequal_individual_state_202");
            $table->integer("unequal_individual_state_203");
            $table->integer("unequal_individual_state_204");
            $table->integer("unequal_individual_state_205");
            $table->integer("unequal_individual_state_206");
            $table->integer("unequal_individual_state_207");
            $table->integer("unequal_individual_state_208");
            $table->date("contract_term_21_on")->nullable();
            $table->double("repayment_principal_21");
            $table->integer("unequal_individual_state_211");
            $table->integer("unequal_individual_state_212");
            $table->integer("unequal_individual_state_213");
            $table->integer("unequal_individual_state_214");
            $table->integer("unequal_individual_state_215");
            $table->integer("unequal_individual_state_216");
            $table->integer("unequal_individual_state_217");
            $table->integer("unequal_individual_state_218");
            $table->date("contract_term_22_on")->nullable();
            $table->double("repayment_principal_22");
            $table->integer("unequal_individual_state_221");
            $table->integer("unequal_individual_state_222");
            $table->integer("unequal_individual_state_223");
            $table->integer("unequal_individual_state_224");
            $table->integer("unequal_individual_state_225");
            $table->integer("unequal_individual_state_226");
            $table->integer("unequal_individual_state_227");
            $table->integer("unequal_individual_state_228");
            $table->date("contract_term_23_on")->nullable();
            $table->double("repayment_principal_23");
            $table->integer("unequal_individual_state_231");
            $table->integer("unequal_individual_state_232");
            $table->integer("unequal_individual_state_233");
            $table->integer("unequal_individual_state_234");
            $table->integer("unequal_individual_state_235");
            $table->integer("unequal_individual_state_236");
            $table->integer("unequal_individual_state_237");
            $table->integer("unequal_individual_state_238");
            $table->date("contract_term_24_on")->nullable();
            $table->double("repayment_principal_24");
            $table->integer("unequal_individual_state_241");
            $table->integer("unequal_individual_state_242");
            $table->integer("unequal_individual_state_243");
            $table->integer("unequal_individual_state_244");
            $table->integer("unequal_individual_state_245");
            $table->integer("unequal_individual_state_246");
            $table->integer("unequal_individual_state_247");
            $table->integer("unequal_individual_state_248");
            $table->date("contract_term_25_on")->nullable();
            $table->double("repayment_principal_25");
            $table->integer("unequal_individual_state_251");
            $table->integer("unequal_individual_state_252");
            $table->integer("unequal_individual_state_253");
            $table->integer("unequal_individual_state_254");
            $table->integer("unequal_individual_state_255");
            $table->integer("unequal_individual_state_256");
            $table->integer("unequal_individual_state_257");
            $table->integer("unequal_individual_state_258");
            $table->date("contract_term_26_on")->nullable();
            $table->double("repayment_principal_26");
            $table->integer("unequal_individual_state_261");
            $table->integer("unequal_individual_state_262");
            $table->integer("unequal_individual_state_263");
            $table->integer("unequal_individual_state_264");
            $table->integer("unequal_individual_state_265");
            $table->integer("unequal_individual_state_266");
            $table->integer("unequal_individual_state_267");
            $table->integer("unequal_individual_state_268");
            $table->date("contract_term_27_on")->nullable();
            $table->double("repayment_principal_27");
            $table->integer("unequal_individual_state_271");
            $table->integer("unequal_individual_state_272");
            $table->integer("unequal_individual_state_273");
            $table->integer("unequal_individual_state_274");
            $table->integer("unequal_individual_state_275");
            $table->integer("unequal_individual_state_276");
            $table->integer("unequal_individual_state_277");
            $table->integer("unequal_individual_state_278");
            $table->date("contract_term_28_on")->nullable();
            $table->double("repayment_principal_28");
            $table->integer("unequal_individual_state_281");
            $table->integer("unequal_individual_state_282");
            $table->integer("unequal_individual_state_283");
            $table->integer("unequal_individual_state_284");
            $table->integer("unequal_individual_state_285");
            $table->integer("unequal_individual_state_286");
            $table->integer("unequal_individual_state_287");
            $table->integer("unequal_individual_state_288");
            $table->date("contract_term_29_on")->nullable();
            $table->double("repayment_principal_29");
            $table->integer("unequal_individual_state_291");
            $table->integer("unequal_individual_state_292");
            $table->integer("unequal_individual_state_293");
            $table->integer("unequal_individual_state_294");
            $table->integer("unequal_individual_state_295");
            $table->integer("unequal_individual_state_296");
            $table->integer("unequal_individual_state_297");
            $table->integer("unequal_individual_state_298");
            $table->date("contract_term_30_on")->nullable();
            $table->double("repayment_principal_30");
            $table->integer("unequal_individual_state_301");
            $table->integer("unequal_individual_state_302");
            $table->integer("unequal_individual_state_303");
            $table->integer("unequal_individual_state_304");
            $table->integer("unequal_individual_state_305");
            $table->integer("unequal_individual_state_306");
            $table->integer("unequal_individual_state_307");
            $table->integer("unequal_individual_state_308");
            $table->date("contract_term_31_on")->nullable();
            $table->double("repayment_principal_31");
            $table->integer("unequal_individual_state_311");
            $table->integer("unequal_individual_state_312");
            $table->integer("unequal_individual_state_313");
            $table->integer("unequal_individual_state_314");
            $table->integer("unequal_individual_state_315");
            $table->integer("unequal_individual_state_316");
            $table->integer("unequal_individual_state_317");
            $table->integer("unequal_individual_state_318");
            $table->date("contract_term_32_on")->nullable();
            $table->double("repayment_principal_32");
            $table->integer("unequal_individual_state_321");
            $table->integer("unequal_individual_state_322");
            $table->integer("unequal_individual_state_323");
            $table->integer("unequal_individual_state_324");
            $table->integer("unequal_individual_state_325");
            $table->integer("unequal_individual_state_326");
            $table->integer("unequal_individual_state_327");
            $table->integer("unequal_individual_state_328");
            $table->date("contract_term_33_on")->nullable();
            $table->double("repayment_principal_33");
            $table->integer("unequal_individual_state_331");
            $table->integer("unequal_individual_state_332");
            $table->integer("unequal_individual_state_333");
            $table->integer("unequal_individual_state_334");
            $table->integer("unequal_individual_state_335");
            $table->integer("unequal_individual_state_336");
            $table->integer("unequal_individual_state_337");
            $table->integer("unequal_individual_state_338");
            $table->date("contract_term_34_on")->nullable();
            $table->double("repayment_principal_34");
            $table->integer("unequal_individual_state_341");
            $table->integer("unequal_individual_state_342");
            $table->integer("unequal_individual_state_343");
            $table->integer("unequal_individual_state_344");
            $table->integer("unequal_individual_state_345");
            $table->integer("unequal_individual_state_346");
            $table->integer("unequal_individual_state_347");
            $table->integer("unequal_individual_state_348");
            $table->date("contract_term_35_on")->nullable();
            $table->double("repayment_principal_35");
            $table->integer("unequal_individual_state_351");
            $table->integer("unequal_individual_state_352");
            $table->integer("unequal_individual_state_353");
            $table->integer("unequal_individual_state_354");
            $table->integer("unequal_individual_state_355");
            $table->integer("unequal_individual_state_356");
            $table->integer("unequal_individual_state_357");
            $table->integer("unequal_individual_state_358");
            $table->date("contract_term_36_on")->nullable();
            $table->double("repayment_principal_36");
            $table->integer("conversion_state");
            $table->integer("repayment_total_count");
            $table->integer("previous_prefecture_code");
            $table->string("spare");
            $table->integer("monthly_id")->index();
            $table->timestamps("");
        });
    }

    public function down() {
        if (Schema::connection($this->connect)->hasTable($this->tableName))
        {
            Schema::connection($this->connect)->drop($this->tableName);
        }
    }

}
