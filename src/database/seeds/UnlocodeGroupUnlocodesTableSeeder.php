<?php

namespace Dc\Unlocodes\Seeds;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class UnlocodeGroupUnlocodesTableSeeder extends CsvSeeder
{

    public function __construct()
    {
        $this->table = 'unlocode_group_unlocodes';
        $this->filename = __DIR__ . '/csvs/unlocode_group_unlocode' . (\App::runningUnitTests() ? '_testing' : '') . '.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'groupname',
            1 => 'countrycode',
            2 => 'placecode'
        ];
        $this->insert_chunk_size = 512;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Recommended when importing larger CSVs
        DB::disableQueryLog();

        // Uncomment the below to wipe the table clean before populating
        // DB::table($this->table)->truncate();

        // echo "Seeding UNLOCode groups\n";
        parent::run();
    }
}
