<?php

namespace Dc\Unlocodes\Seeds;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\DB;

class UnlocodeGroupsTableSeeder extends CsvSeeder
{

    public function __construct()
    {
        $this->table = 'unlocode_groups';
        $this->filename = __DIR__ . '/csvs/unlocode_group.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'name',
            1 => 'type',
            2 => 'description'
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
        DB::table($this->table)->truncate();

        // echo "Seeding UNLOCode group definitions\n";
        parent::run();
    }
}
