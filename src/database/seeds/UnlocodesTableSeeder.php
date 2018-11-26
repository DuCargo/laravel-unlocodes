<?php

namespace Dc\Unlocodes\Seeds;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class UnlocodesTableSeeder extends CsvSeeder
{

    public function __construct()
    {
        $this->table = 'unlocodes';
        $this->filename = __DIR__ . '/csvs/unlocode' . (\App::runningUnitTests() ? '_testing' : '') . '.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'countrycode',
            1 => 'placecode',
            2 => 'name',
            3 => 'longitude',
            4 => 'latitude',
            5 => 'subdivision',
            6 => 'status',
            7 => 'date',
            8 => 'IATA'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Recommended when importing larger CSVs
        DB::disableQueryLog();

        // Uncomment the below to wipe the table clean before populating
        //DB::table($this->table)->truncate();

        // echo "Seeding UNLOCodes\n";
        //parent::run();
    }
}
