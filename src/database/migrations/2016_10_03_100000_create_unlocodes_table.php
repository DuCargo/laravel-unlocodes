<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnlocodesTable extends Migration
{
    private $driver = '';

    function __construct()
    {
        $connection = config('database.default');
        $this->driver = config("database.connections.{$connection}.driver");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQL:
        // countrycode 		CHAR(2) NOT NULL,
        // placecode 			CHAR(3) NOT NULL,
        // name 				VARCHAR(100) NOT NULL,
        // longitude 			FLOAT DEFAULT NULL,
        // latitude 			FLOAT DEFAULT NULL,
        // subdivision			CHAR(3) DEFAULT '',
        // status				CHAR(2) DEFAULT '',
        // date				CHAR(4) DEFAULT '',
        // IATA				CHAR(3) DEFAULT '',
        // PRIMARY KEY ( countrycode, placecode ),
        // UNIQUE location ( name, subdivision, countrycode )

        // Change,Country,Location,Name,NameWoDiacritics,Subdivision,Status,Function,Date,IATA,Coordinates,Remarks

        Schema::create('unlocodes', function (Blueprint $table) {
            $table->string('countrycode', 2);
            $table->string('placecode', 3);
            $table->string('name', 100);
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();
            $table->string('subdivision', 3)->nullable()->default('');
            $table->string('status', 2)->nullable()->default('');
            $table->string('date', 4)->nullable()->default('');
            $table->string('IATA', 3)->nullable()->default('');

            $table->timestamps();

            $table->primary(['countrycode', 'placecode']);
            $table->index(['name', 'subdivision', 'countrycode'], 'location');
        });

        // FULLTEXT index on placecode + countrycode + u.name + subdivision (disabled when using sqlite)
        if ($this->driver !== 'sqlite') {
            DB::statement('ALTER TABLE unlocodes ADD FULLTEXT INDEX full (placecode, name, subdivision, countrycode)');
        }

        Artisan::call('db:seed', array('--class' => 'Dc\Unlocodes\Seeds\UnlocodesTableSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unlocodes');
    }
}
