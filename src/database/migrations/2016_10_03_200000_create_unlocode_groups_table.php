<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Dc\Unlocodes\Seeds\UnlocodeGroupsTableSeeder;

class CreateUnlocodeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQL:
        // name 		VARCHAR( 80 ) NOT NULL,
        // type 		VARCHAR( 60 ) DEFAULT NULL,
        // description TEXT DEFAULT NULL,
        // PRIMARY KEY (name)

        Schema::create('unlocode_groups', function (Blueprint $table) {
            $table->string('name', 80);
            $table->string('type', 60)->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->primary('name');
        });

        Artisan::call('db:seed', array('--class' => 'Dc\Unlocodes\Seeds\UnlocodeGroupsTableSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlocode_group_unlocodes');
        Schema::dropIfExists('unlocode_groups');
    }
}
