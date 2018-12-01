<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Dc\Unlocodes\Seeds\UnlocodeGroupUnlocodesTableSeeder;

class CreateUnlocodeAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'unlocode_aliases',
            function (Blueprint $table) {
                $table->string('unlocode', 5);
                $table->string('alias', 100);

                $table->timestamps();

                $table->primary(['unlocode', 'alias']);
                $table->foreign('unlocode')
                    ->references('unlocode')->on('unlocodes')
                    ->onDelete('cascade')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlocode_aliases');
    }
}
