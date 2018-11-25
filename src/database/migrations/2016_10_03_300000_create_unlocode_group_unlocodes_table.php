<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Dc\Unlocodes\Seeds\UnlocodeGroupUnlocodesTableSeeder;

class CreateUnlocodeGroupUnlocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQL:
        // groupname 		VARCHAR(80) DEFAULT NULL,
        // countrycode 	CHAR(2) DEFAULT NULL,
        // placecode 		CHAR(3) DEFAULT NULL,
        // PRIMARY KEY (groupname, countrycode, placecode),
        // FOREIGN KEY (groupname) REFERENCES unlocode_group(name) ON DELETE CASCADE ON UPDATE CASCADE,
        // FOREIGN KEY (countrycode, placecode) REFERENCES unlocode(countrycode, placecode) ON DELETE CASCADE ON UPDATE CASCADE

        Schema::create('unlocode_group_unlocodes', function (Blueprint $table) {
            $table->string('groupname', 80)->nullable();
            $table->string('countrycode', 2)->nullable();
            $table->string('placecode', 3)->nullable();

            $table->timestamps();

            $table->primary(['groupname', 'countrycode', 'placecode']);
            $table->foreign('groupname')
                ->references('name')->on('unlocode_groups')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['countrycode', 'placecode'])
                ->references(['countrycode', 'placecode'])->on('unlocodes')
                ->onDelete('cascade')->onUpdate('cascade');
        });

        Artisan::call('db:seed', array('--class' => 'Dc\Unlocodes\Seeds\UnlocodeGroupUnlocodesTableSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlocode_group_unlocodes');
    }
}
